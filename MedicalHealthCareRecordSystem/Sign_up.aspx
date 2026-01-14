<%@ Page Language="C#" AutoEventWireup="true" CodeFile="Sign_up.aspx.cs" Inherits="SignUp" %>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet"/>
     <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
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
                        
                <!--loader 1-->
                <div runat="server" id="loader1" class="container d-flex justify-content-center align-items-center">
                    <div class="progresses">
                        <div class="steps-current">
                            <span class=" fw-bold">1</span>
                        </div>

                        <span class="line-second"></span>

                        <div class="steps-next">
                            <span class="fw-bold">2</span>
                        </div>
                        <span class="line-second"></span>

                        <div class="steps-next">
                            <span class="fw-bold">3</span>
                        </div>

                    </div>
                </div>
                  <!--card 1-->
                        <div class="d-flex justify-content-center">
                            
                            <div runat="server" id="card1" class="card-glass pt-4 pb-3">
                                <div class="card-header bg-transparent border-bottom-0">
                                    <h2 class="text-capitalize custom-h2 Inter-title">Create an Account</h2>
                                </div>
                                <div class="card-body bg-transparent pt-0 Inter-p">
                                    <p class="text-muted">
                                        <small>Sign up now to access personalized health insight, connect with expert, and take change of your well-being.</small>
                                    </p>
                                    <asp:TextBox ID="txtusername" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Username" />
                                    <asp:RequiredFieldValidator ValidationGroup="grp1" ErrorMessage="required" ForeColor="#DC4637" Display="Dynamic" ControlToValidate="txtusername" runat="server" />

                                    <asp:TextBox ID="txtemail" TextMode="Email" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Email Address" />
                                    <asp:RequiredFieldValidator ValidationGroup="grp1" ErrorMessage="required" ForeColor="#DC4637" Display="Dynamic" ControlToValidate="txtemail" runat="server" />

                                    <asp:TextBox TextMode="Password" ID="txtpassword" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Password" />
                                    <asp:RequiredFieldValidator ValidationGroup="grp1" ErrorMessage="required" ForeColor="#DC4637" Display="Dynamic" ControlToValidate="txtpassword" runat="server" />

                                    <asp:TextBox TextMode="Password" ID="txtconpassword" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Confirm password" />
                                    <asp:RequiredFieldValidator ValidationGroup="grp1" ErrorMessage="required" ForeColor="#DC4637" Display="Dynamic" ControlToValidate="txtconpassword" runat="server" />
                                    <asp:CompareValidator ValidationGroup="grp1" ErrorMessage="Password does not match" ForeColor="#DC4637" Display="Dynamic" ControlToValidate="txtconpassword" runat="server" ControlToCompare="txtpassword" />
                                   <%-- <div class="d-flex justify-content-between">
                                        <span>
                                            <asp:CheckBox runat="server" ID="checkAgree" />
                                            I agree to all Term & Condition and Privacy Policy</span>
                                    </div>--%>
                                    <asp:Button ValidationGroup="grp1" ID="next" OnClick="next_Click" OnClientClick="showLoader(); return true;" Text="Next" CssClass="btn btn-primary w-100 mt-2" runat="server" />
                                    <p class="text-dar">Already have an account?<a href="login.aspx"> Sign in now!</a></p> 
                                  

                                    <div class=" text-center">
                                        <a class=" text-decoration-none text-dark mt-3" runat="server" href="Home.aspx"><i class="fa-solid fa-arrow-left"></i><span>back to home</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <asp:Label Text="" ID="txtvisibletext" Visible="true" runat="server" />
                <!--loader 2-->
                <div runat="server" id="loader2" visible="false" class="container d-flex justify-content-center align-items-center">
                    <div class="progresses">
                        <div class="steps-current">
                            <span class=" fw-bold"><i class="fa fa-check"></i></span>
                        </div>

                        <span class="line-first"></span>

                        <div class="steps-current">
                            <span class="fw-bold">2</span>
                        </div>
                        <span class="line-second"></span>

                        <div class="steps-next">
                            <span class="fw-bold">3</span>
                        </div>

                    </div>
                </div>
                        
                  <!--card 2-->
                <div runat="server" id="verificationerror" visible="false">
                    <div class=" position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                        <div class=" toast show bg-danger ">
                            <div class="toast-body">
                                <p class="fs-6">Verification code does not match. Please try again.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <div runat="server" id="card2" visible="false" class="card-glass pt-4 pb-3">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h2 class="text-capitalize custom-h2 Inter-title">Verify your Account</h2>
                        </div>
                        <div class="card-body bg-transparent pt-0 Inter-p">
                            <p class="text-muted">
                                <small>We have sent a verification code. Please check your Gmail account to verify your email address.</small>
                            </p>
                            <asp:TextBox ID="txtverificationCode" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Verification Code" />

                            <asp:Button ID="btnConfirm" Text="Verify" CssClass="btn btn-primary w-100 mt-2" runat="server" OnClientClick="showLoader(); return true;" OnClick="btnConfirm_Click" />

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
                                        <p class="fs-6">
                                            This email address is already registered. If you have an existing account, please <a href="login.aspx">sign in</a>
                                            or visit the
                                    <a href="Forgot_Password.aspx">forgot password</a>
                                            page if you need to reset your password.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </ContentTemplate>
                    <Triggers>
                    </Triggers>
                </asp:UpdatePanel>
            </div>
        </div>
    </form>
    <script language="javascript" type="text/javascript">
               $(document).ready(function () {
                   $('#<%=error.ClientID%>').fadeOut(10000, function () {
                                    $(this).html("");
                                });
                            });
    </script>
    <script type="text/javascript">
        function showLoader() {
            document.getElementById("loader-overlay").style.display = "flex";
        }
</script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
