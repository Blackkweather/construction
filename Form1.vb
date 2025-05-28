Imports MySql.Data.MySqlClient


Public Class Form1

    ' Connection string for MAMP MySQL - updated to use default MySQL port 3306
    Private connectionString As String = "server=localhost;port=3306;user id=root;password=root;database=bibliotheque;"

    Private labelTitre As Label
    Private labelAuteur As Label
    Private labelCategorie As Label
    Private labelAnnee As Label
    Private labelISBN As Label

    Private Sub Form1_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        InitializeLabels()
        TesterConnexion()
        ChargerAuteursEtCategories()
        ChargerLivres()
    End Sub

    Private Sub InitializeLabels()
        ' Initialize labels for each input field on the left side
        labelTitre = New Label()
        labelTitre.Text = "Titre du livre:"
        labelTitre.Location = New Drawing.Point(txtTitre.Location.X - 120, txtTitre.Location.Y + 3)
        labelTitre.AutoSize = True
        Me.Controls.Add(labelTitre)

        labelAuteur = New Label()
        labelAuteur.Text = "Auteur (sélectionnez):"
        labelAuteur.Location = New Drawing.Point(cboAuteur.Location.X - 120, cboAuteur.Location.Y + 3)
        labelAuteur.AutoSize = True
        Me.Controls.Add(labelAuteur)

        labelCategorie = New Label()
        labelCategorie.Text = "Catégorie (sélectionnez):"
        labelCategorie.Location = New Drawing.Point(cboCategorie.Location.X - 120, cboCategorie.Location.Y + 3)
        labelCategorie.AutoSize = True
        Me.Controls.Add(labelCategorie)

        labelAnnee = New Label()
        labelAnnee.Text = "Année de publication:"
        labelAnnee.Location = New Drawing.Point(txtAnnee.Location.X - 120, txtAnnee.Location.Y + 3)
        labelAnnee.AutoSize = True
        Me.Controls.Add(labelAnnee)

        labelISBN = New Label()
        labelISBN.Text = "Numéro ISBN:"
        labelISBN.Location = New Drawing.Point(txtISBN.Location.X - 120, txtISBN.Location.Y + 3)
        labelISBN.AutoSize = True
        Me.Controls.Add(labelISBN)
    End Sub

    Private Sub PositionButtons()
        ' Position buttons on the right side with aligned vertical centers and consistent spacing
        Dim rightX As Integer = dgvLivres.Location.X + dgvLivres.Width + 20
        Dim startY As Integer = 50
        Dim spacing As Integer = btnAjouter.Height + 10 ' spacing based on button height plus margin
        Dim buttonWidth As Integer = 100 ' fixed width for all buttons
        Dim buttonHeight As Integer = btnAjouter.Height

        btnAjouter.Size = New Drawing.Size(buttonWidth, buttonHeight)
        btnModifier.Size = New Drawing.Size(buttonWidth, buttonHeight)
        btnSupprimer.Size = New Drawing.Size(buttonWidth, buttonHeight)

        btnAjouter.Location = New Drawing.Point(rightX, startY)
        btnModifier.Location = New Drawing.Point(rightX, startY + spacing)
        btnSupprimer.Location = New Drawing.Point(rightX, startY + 2 * spacing)
    End Sub

    Private Sub Form1_Shown(sender As Object, e As EventArgs) Handles Me.Shown
        PositionButtons()
    End Sub

    ' Test the connection to the database
    Private Sub TesterConnexion()
        Try
            Using conn As New MySqlConnection(connectionString)
                conn.Open()
                MessageBox.Show("Connexion réussie à la base de données MAMP !")
            End Using
        Catch ex As Exception
            MessageBox.Show("Erreur de connexion : " & ex.Message)
        End Try
    End Sub

    ' Load authors and categories into ComboBoxes
    Private Sub ChargerAuteursEtCategories()
        Try
            Using conn As New MySqlConnection(connectionString)
                conn.Open()
                ' Authors
                Dim dtAuteurs As New DataTable()
                Using cmd As New MySqlCommand("SELECT IdAuteur, CONCAT(PrenomAuteur, ' ', NomAuteur) AS NomComplet FROM Auteurs", conn)
                    Using adapter As New MySqlDataAdapter(cmd)
                        adapter.Fill(dtAuteurs)
                    End Using
                End Using
                cboAuteur.DataSource = dtAuteurs
                cboAuteur.DisplayMember = "NomComplet"
                cboAuteur.ValueMember = "IdAuteur"

                ' Categories
                Dim dtCategories As New DataTable()
                Using cmd As New MySqlCommand("SELECT IdCategorie, NomCategorie FROM Categories", conn)
                    Using adapter As New MySqlDataAdapter(cmd)
                        adapter.Fill(dtCategories)
                    End Using
                End Using
                cboCategorie.DataSource = dtCategories
                cboCategorie.DisplayMember = "NomCategorie"
                cboCategorie.ValueMember = "IdCategorie"
            End Using
        Catch ex As Exception
            MessageBox.Show("Erreur chargement auteurs/catégories : " & ex.Message)
        End Try
    End Sub

    ' Load books into DataGridView
    Private Sub ChargerLivres()
        Try
            Using conn As New MySqlConnection(connectionString)
                conn.Open()
                Dim query As String = "SELECT Livres.IdLivre, Livres.Titre, Auteurs.NomAuteur, Categories.NomCategorie, Livres.AnneePublication, Livres.ISBN FROM Livres INNER JOIN Auteurs ON Livres.IdAuteur = Auteurs.IdAuteur INNER JOIN Categories ON Livres.IdCategorie = Categories.IdCategorie"
                Using cmd As New MySqlCommand(query, conn)
                    Using adapter As New MySqlDataAdapter(cmd)
                        Dim dt As New DataTable()
                        adapter.Fill(dt)
                        dgvLivres.DataSource = dt
                    End Using
                End Using
            End Using
        Catch ex As Exception
            MessageBox.Show("Erreur chargement livres : " & ex.Message)
        End Try
    End Sub

    ' Add a book
    Private Sub btnAjouter_Click(sender As Object, e As EventArgs) Handles btnAjouter.Click
        If ValiderChamps() Then
            Try
                Using conn As New MySqlConnection(connectionString)
                    conn.Open()
                    Dim query As String = "INSERT INTO Livres (Titre, IdAuteur, IdCategorie, AnneePublication, ISBN) VALUES (@Titre, @IdAuteur, @IdCategorie, @AnneePublication, @ISBN)"
                    Using cmd As New MySqlCommand(query, conn)
                        cmd.Parameters.AddWithValue("@Titre", txtTitre.Text)
                        cmd.Parameters.AddWithValue("@IdAuteur", cboAuteur.SelectedValue)
                        cmd.Parameters.AddWithValue("@IdCategorie", cboCategorie.SelectedValue)
                        cmd.Parameters.AddWithValue("@AnneePublication", If(String.IsNullOrWhiteSpace(txtAnnee.Text), DBNull.Value, txtAnnee.Text))
                        cmd.Parameters.AddWithValue("@ISBN", txtISBN.Text)
                        cmd.ExecuteNonQuery()
                    End Using
                End Using
                ChargerLivres()
            Catch ex As Exception
                MessageBox.Show("Erreur ajout : " & ex.Message)
            End Try
        End If
    End Sub

    ' Edit a book
    Private Sub btnModifier_Click(sender As Object, e As EventArgs) Handles btnModifier.Click
        If ValiderChamps() AndAlso dgvLivres.CurrentRow IsNot Nothing Then
            Try
                Dim idLivre As Integer = Convert.ToInt32(dgvLivres.CurrentRow.Cells("IdLivre").Value)
                Using conn As New MySqlConnection(connectionString)
                    conn.Open()
                    Dim query As String = "UPDATE Livres SET Titre=@Titre, IdAuteur=@IdAuteur, IdCategorie=@IdCategorie, AnneePublication=@AnneePublication, ISBN=@ISBN WHERE IdLivre=@IdLivre"
                    Using cmd As New MySqlCommand(query, conn)
                        cmd.Parameters.AddWithValue("@Titre", txtTitre.Text)
                        cmd.Parameters.AddWithValue("@IdAuteur", cboAuteur.SelectedValue)
                        cmd.Parameters.AddWithValue("@IdCategorie", cboCategorie.SelectedValue)
                        cmd.Parameters.AddWithValue("@AnneePublication", If(String.IsNullOrWhiteSpace(txtAnnee.Text), DBNull.Value, txtAnnee.Text))
                        cmd.Parameters.AddWithValue("@ISBN", txtISBN.Text)
                        cmd.Parameters.AddWithValue("@IdLivre", idLivre)
                        cmd.ExecuteNonQuery()
                    End Using
                End Using
                ChargerLivres()
            Catch ex As Exception
                MessageBox.Show("Erreur modification : " & ex.Message)
            End Try
        End If
    End Sub

    ' Delete a book
    Private Sub btnSupprimer_Click(sender As Object, e As EventArgs) Handles btnSupprimer.Click
        If dgvLivres.CurrentRow IsNot Nothing AndAlso MessageBox.Show("Confirmer la suppression ?", "Confirmation", MessageBoxButtons.YesNo) = DialogResult.Yes Then
            Try
                Dim idLivre As Integer = Convert.ToInt32(dgvLivres.CurrentRow.Cells("IdLivre").Value)
                Using conn As New MySqlConnection(connectionString)
                    conn.Open()
                    Dim query As String = "DELETE FROM Livres WHERE IdLivre=@IdLivre"
                    Using cmd As New MySqlCommand(query, conn)
                        cmd.Parameters.AddWithValue("@IdLivre", idLivre)
                        cmd.ExecuteNonQuery()
                    End Using
                End Using
                ChargerLivres()
            Catch ex As Exception
                MessageBox.Show("Erreur suppression : " & ex.Message)
            End Try
        End If
    End Sub

    ' Validate fields
    Private Function ValiderChamps() As Boolean
        If String.IsNullOrWhiteSpace(txtTitre.Text) Then
            MessageBox.Show("Le titre est obligatoire.")
            Return False
        End If
        If cboAuteur.SelectedIndex = -1 Then
            MessageBox.Show("Sélectionnez un auteur.")
            Return False
        End If
        If cboCategorie.SelectedIndex = -1 Then
            MessageBox.Show("Sélectionnez une catégorie.")
            Return False
        End If
        Return True
    End Function

End Class
