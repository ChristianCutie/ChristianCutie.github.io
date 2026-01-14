<%@ Page Language="C#" AutoEventWireup="true" CodeFile="login.aspx.cs" Inherits="login" %>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet"/>
    <link href="css/custom.css" rel="stylesheet" />
     <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
  <link rel="shortcut icon" href="image/Master Card Logo Now Is The Time For You To Know The Truth About Master Card Logo - AH – STUDIO Blog - Copy.jpeg" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"/>
    <style>
        .large-checkbox input {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        #loader-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7); /* Black with 70% opacity */
            z-index: 9999; /* Ensure it is on top */
            display: flex;
            justify-content: center;
            align-items: center;
        }

            /* Optional: Style the loading GIF */
            #loader-overlay img {
                width: 60px; /* Adjust size as needed */
                height: 60px;
            }
    </style>
</head>
    
<body>
    <form id="form1" runat="server">
        <div class="bg-custom">
            <img width="100" height="100" src="image/Master Card Logo Now Is The Time For You To Know The Truth About Master Card Logo - AH – STUDIO Blog - Copy.jpeg" alt="" />
            <div class=" container">
                <div class="d-flex justify-content-center">
                    <div class="card-glass pt-4 pb-4">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h2 class="text-capitalize custom-h2 Inter-title">welcome</h2>
                        </div>
                        <div class="card-body bg-transparent pt-0 Inter-p">
                            <p class="text-muted">
                                <small>Please fill up the form  below to get started</small>
                            </p>

                            <asp:Label Text="Email Address" CssClass="form-label fw-bold" runat="server" />
                            <asp:TextBox ID="txtemail" runat="server" CssClass="form-control mb-3" BorderWidth="1px" />

                            <asp:Label Text="Password" CssClass="form-label fw-bold" runat="server" />
                            <asp:TextBox ID="txtpassword" TextMode="Password" runat="server" CssClass="form-control mb-3" BorderWidth="1px" />
                            <div class="d-flex justify-content-between">
                                <span>
                                    <asp:CheckBox CssClass="large-checkbox" runat="server" ID="chkShowPassword" onclick="togglePassword()" />
                                    <asp:Label Text=" Show Password" runat="server" /> </span>
                                <p class=" text-capitalize" style="color: #0077B6;"><a href="Forgot_Password.aspx">forgot password</a></p>
                            </div>
                            <asp:Button Text="Login" ID="btnlogin" OnClick="btnlogin_Click" CssClass="btn btn-primary w-100 mt-2" runat="server" OnClientClick="showLoader(); return true;" />
                            <p class="text-dar">Don't have an account? <a href="Sign_up.aspx">Sign up now!</a></p>

                            <div class=" text-center">
                                <a class=" text-decoration-none text-dark mt-3" runat="server" href="Home.aspx"><i class="fa-solid fa-arrow-left"></i><span>back to home</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="loader-overlay" style=" display:none;">
                         <img id="loader" src="loader/loader-rounded.gif" alt="Loading..." style=" position: fixed; top: 50%; left: 50%; width: 70px; height: 70px;" />
                            </div>
                <div runat="server" id="error" visible="false">
                    <div class=" position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                        <div class=" toast show bg-warning ">
                            <div class="toast-body">
                                <asp:Label ID="lblerror" runat="server" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
     <script type="text/javascript">
         function showLoader() {
             document.getElementById("loader-overlay").style.display = "flex";
         }
     </script>
    <script type="text/javascript">
        function togglePassword() {
            var passwordField = document.getElementById('<%= txtpassword.ClientID %>');
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
</script>
     <script language="javascript" type="text/javascript">
         $(document).ready(function () {
             $('#<%=error.ClientID%>').fadeOut(5000, function () {
                $(this).html("");
            });
        });
     </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
</body>
</html>
