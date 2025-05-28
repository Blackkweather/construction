Imports System.Windows.Forms

Public Class FormChoice
    Inherits Form

    Private labelTitle As Label
    Private btnGestionAuteurs As Button
    Private btnGestionLivres As Button

    Public Sub New()
        InitializeComponent()
    End Sub

    Private Sub InitializeComponent()
        Me.labelTitle = New Label()
        Me.btnGestionAuteurs = New Button()
        Me.btnGestionLivres = New Button()

        ' 
        ' labelTitle
        ' 
        Me.labelTitle.AutoSize = True
        Me.labelTitle.Font = New Drawing.Font("Segoe UI", 14.0!, Drawing.FontStyle.Bold)
        Me.labelTitle.Location = New Drawing.Point(50, 20)
        Me.labelTitle.Name = "labelTitle"
        Me.labelTitle.Size = New Drawing.Size(300, 25)
        Me.labelTitle.Text = "Choisissez une gestion à ouvrir"
        Me.labelTitle.TextAlign = Drawing.ContentAlignment.MiddleCenter

        ' 
        ' btnGestionAuteurs
        ' 
        Me.btnGestionAuteurs.Location = New Drawing.Point(100, 70)
        Me.btnGestionAuteurs.Name = "btnGestionAuteurs"
        Me.btnGestionAuteurs.Size = New Drawing.Size(200, 40)
        Me.btnGestionAuteurs.Text = "Gestionnaire d'Auteurs"
        AddHandler Me.btnGestionAuteurs.Click, AddressOf Me.BtnGestionAuteurs_Click

        ' 
        ' btnGestionLivres
        ' 
        Me.btnGestionLivres.Location = New Drawing.Point(100, 130)
        Me.btnGestionLivres.Name = "btnGestionLivres"
        Me.btnGestionLivres.Size = New Drawing.Size(200, 40)
        Me.btnGestionLivres.Text = "Gestionnaire de Livres"
        AddHandler Me.btnGestionLivres.Click, AddressOf Me.BtnGestionLivres_Click

        ' 
        ' FormChoice
        ' 
        Me.ClientSize = New Drawing.Size(400, 200)
        Me.Controls.Add(Me.labelTitle)
        Me.Controls.Add(Me.btnGestionAuteurs)
        Me.Controls.Add(Me.btnGestionLivres)
        Me.Name = "FormChoice"
        Me.Text = "Menu Principal"
        Me.StartPosition = FormStartPosition.CenterScreen
    End Sub

    Private Sub BtnGestionAuteurs_Click(sender As Object, e As EventArgs)
        ' Open the authors management form
        Dim formAuteurs As New FormAuteurs()
        formAuteurs.Show()
        Me.Hide()
    End Sub

    Private Sub BtnGestionLivres_Click(sender As Object, e As EventArgs)
        ' Open the books management form (assuming Form1 is for books)
        Dim formLivres As New Form1()
        formLivres.Show()
        Me.Hide()
    End Sub
End Class
