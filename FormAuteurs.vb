Imports MySql.Data.MySqlClient

Public Class FormAuteurs

    ' Connection string for MAMP MySQL
    Private connectionString As String = "server=localhost;port=3306;user id=root;password=root;database=bibliotheque;"

    Private Sub FormAuteurs_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        TesterConnexion()
        ChargerAuteurs()
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

    ' Load authors into DataGridView
    Private Sub ChargerAuteurs()
        Try
            Using conn As New MySqlConnection(connectionString)
                conn.Open()
                Dim query As String = "SELECT IdAuteur, PrenomAuteur, NomAuteur FROM Auteurs"
                Using cmd As New MySqlCommand(query, conn)
                    Using adapter As New MySqlDataAdapter(cmd)
                        Dim dt As New DataTable()
                        adapter.Fill(dt)
                        dgvAuteurs.DataSource = dt
                    End Using
                End Using
            End Using
        Catch ex As Exception
            MessageBox.Show("Erreur chargement auteurs : " & ex.Message)
        End Try
    End Sub

    ' Add an author
    Private Sub btnAjouter_Click(sender As Object, e As EventArgs) Handles btnAjouter.Click
        If ValiderChamps() Then
            Try
                Using conn As New MySqlConnection(connectionString)
                    conn.Open()
                    Dim query As String = "INSERT INTO Auteurs (PrenomAuteur, NomAuteur) VALUES (@Prenom, @Nom)"
                    Using cmd As New MySqlCommand(query, conn)
                        cmd.Parameters.AddWithValue("@Prenom", txtPrenom.Text)
                        cmd.Parameters.AddWithValue("@Nom", txtNom.Text)
                        cmd.ExecuteNonQuery()
                    End Using
                End Using
                ChargerAuteurs()
            Catch ex As Exception
                MessageBox.Show("Erreur ajout : " & ex.Message)
            End Try
        End If
    End Sub

    ' Edit an author
    Private Sub btnModifier_Click(sender As Object, e As EventArgs) Handles btnModifier.Click
        If ValiderChamps() AndAlso dgvAuteurs.CurrentRow IsNot Nothing Then
            Try
                Dim idAuteur As Integer = Convert.ToInt32(dgvAuteurs.CurrentRow.Cells("IdAuteur").Value)
                Using conn As New MySqlConnection(connectionString)
                    conn.Open()
                    Dim query As String = "UPDATE Auteurs SET PrenomAuteur=@Prenom, NomAuteur=@Nom WHERE IdAuteur=@IdAuteur"
                    Using cmd As New MySqlCommand(query, conn)
                        cmd.Parameters.AddWithValue("@Prenom", txtPrenom.Text)
                        cmd.Parameters.AddWithValue("@Nom", txtNom.Text)
                        cmd.Parameters.AddWithValue("@IdAuteur", idAuteur)
                        cmd.ExecuteNonQuery()
                    End Using
                End Using
                ChargerAuteurs()
            Catch ex As Exception
                MessageBox.Show("Erreur modification : " & ex.Message)
            End Try
        End If
    End Sub

    ' Delete an author
    Private Sub btnSupprimer_Click(sender As Object, e As EventArgs) Handles btnSupprimer.Click
        If dgvAuteurs.CurrentRow IsNot Nothing AndAlso MessageBox.Show("Confirmer la suppression ?", "Confirmation", MessageBoxButtons.YesNo) = DialogResult.Yes Then
            Try
                Dim idAuteur As Integer = Convert.ToInt32(dgvAuteurs.CurrentRow.Cells("IdAuteur").Value)
                Using conn As New MySqlConnection(connectionString)
                    conn.Open()
                    Dim query As String = "DELETE FROM Auteurs WHERE IdAuteur=@IdAuteur"
                    Using cmd As New MySqlCommand(query, conn)
                        cmd.Parameters.AddWithValue("@IdAuteur", idAuteur)
                        cmd.ExecuteNonQuery()
                    End Using
                End Using
                ChargerAuteurs()
            Catch ex As Exception
                MessageBox.Show("Erreur suppression : " & ex.Message)
            End Try
        End If
    End Sub

    ' Validate fields
    Private Function ValiderChamps() As Boolean
        If String.IsNullOrWhiteSpace(txtPrenom.Text) Then
            MessageBox.Show("Le prénom est obligatoire.")
            Return False
        End If
        If String.IsNullOrWhiteSpace(txtNom.Text) Then
            MessageBox.Show("Le nom est obligatoire.")
            Return False
        End If
        Return True
    End Function

End Class
