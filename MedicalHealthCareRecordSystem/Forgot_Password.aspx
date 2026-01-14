<%@ Page Language="C#" AutoEventWireup="true" CodeFile="Forgot_Password.aspx.cs" Inherits="SignUp" %>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet"/>
    <link href="css/custom.css" rel="stylesheet" />
  <link rel="shortcut icon" href="image/Master Card Logo Now Is The Time For You To Know The Truth About Master Card Logo - AH – STUDIO Blog - Copy.jpeg" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"/>
     <style>
        /* Loader Overlay Styles */
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
        <asp:ScriptManager runat="server" />
        <div class="bg-custom">
            <img width="100" height="100" src="image/Master Card Logo Now Is The Time For You To Know The Truth About Master Card Logo - AH – STUDIO Blog - Copy.jpeg" alt="" />
            <div class="container">
                <asp:UpdatePanel runat="server">
                    <ContentTemplate>
                        <div class="d-flex justify-content-center">
                            <div runat="server" id="card1" class="card-glass pt-4 pb-3">
                                <div class="card-header bg-transparent border-bottom-0">
                                    <h2 class="text-capitalize custom-h2 Inter-title">Verify your Account</h2>
                                </div>
                                <div class="card-body bg-transparent pt-0 Inter-p">
                                    <p class="text-muted">
                                        <small>We have to check your credentials if valid.</small>
                                    </p>
                                    <asp:TextBox ID="txtemail" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Email Address" />

                                    <asp:TextBox ID="txtphonenumber" TextMode="Phone" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Phone number" />

                                    <asp:Button ID="btnConfirm" Text="Verify" CssClass="btn btn-primary w-100 mt-2" runat="server" OnClick="btnConfirm_Click" OnClientClick="showLoader(); return true;" />
                                </div>
                                <div class=" text-center">
                                        <a class=" text-decoration-none text-dark mt-3" runat="server" href="login.aspx"><i class="fa-solid fa-arrow-left"></i><span>back to sign in</span></a>
                                    </div>
                            </div>
                            <div runat="server" id="card2" visible="false" class="card-glass pt-4 pb-3">
                                <div class="card-header bg-transparent border-bottom-0">
                                    <h2 class="text-capitalize custom-h2 Inter-title">Verify your Account</h2>
                                </div>
                                <div class="card-body bg-transparent pt-0 Inter-p">
                                    <p class="text-muted">
                                        <small>We have to check your credentials if valid.</small>
                                    </p>
                                    <asp:TextBox ID="txtnewpassword" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="New password" />

                                    <asp:TextBox ID="txtconfirmpassword" TextMode="Phone" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Confirm Password" />
                                    <asp:CompareValidator ValidationGroup="btn1" ControlToCompare="txtnewpassword" ErrorMessage="Password does not match" ForeColor="Red" Display="Dynamic" ControlToValidate="txtconfirmpassword" runat="server" />

                                    <asp:Button ID="btnsave" Text="Save changes" CssClass="btn btn-primary w-100 mt-2" runat="server" ValidationGroup="btn1" OnClick="btnsave_Click" OnClientClick="showLoader(); return true;"/>
                                </div>
                            </div>
                        </div>
                        <div id="loader-overlay"  style="display: none;">
                            <img id="loader" src="loader/loader-rounded.gif" alt="Loading..." style="position: fixed; top: 50%; left: 50%; width: 70px; height: 70px;" />
                        </div>
                        <div runat="server" id="error" visible="false">
                            <div class=" position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                                <div class=" toast show bg-warning ">
                                    <div class="toast-body">
                                        <p class="fs-6">Invalid: The email and phone number you entered do not match. Please try again.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </ContentTemplate>
                    <Triggers>
                        <asp:PostBackTrigger ControlID="btnsave" />
                    </Triggers>
                </asp:UpdatePanel>
            </div>
        </div>
    </form>
    <script type="text/javascript">
        function showLoader() {
            document.getElementById("loader-overlay").style.display = "flex";
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
