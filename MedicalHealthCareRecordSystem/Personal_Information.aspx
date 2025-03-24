<%@ Page Language="C#" AutoEventWireup="true" CodeFile="Personal_Information.aspx.cs" Inherits="login" %>

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
     <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"/>
</head>
<body>
    <form id="form1" runat="server">
        <div class="bg-custom">
            <img width="100" height="100" src="image/Master Card Logo Now Is The Time For You To Know The Truth About Master Card Logo - AH – STUDIO Blog - Copy.jpeg" alt="" />
            <div class=" container">
                <div runat="server" class="container d-flex justify-content-center align-items-center">
                    <div class="progresses">
                        <div class="steps-current">
                            <span class=" fw-bold"><i class="fa fa-check"></i></span>
                        </div>

                        <span class="line-first"></span>

                        <div class="steps-current">
                            <span class=" fw-bold"><i class="fa fa-check"></i></span>
                        </div>
                        <span class="line-first"></span>

                        <div class="steps-next">
                            <span class="fw-bold">3</span>
                        </div>
                    </div>
                </div>
                <asp:Label Text="" ID="lblvisibleusername" Visible="true" runat="server" />
                <asp:Label Text="" ID="lblvisiblepassword" Visible="true" runat="server" />
                <asp:Label Text="" ID="lblvisibleemail" Visible="false" runat="server" />
                <div class="d-flex justify-content-center">
                <div style="width: 40rem !important" class="card-glass pt-4 pb-3">
                    <div class="card-header bg-transparent border-bottom-0">
                        <h2 class="text-capitalize custom-h2 Inter-title">Personal Information</h2>
                    </div>
                    <div class="card-body bg-transparent pt-0 Inter-p">
                        <p class="text-muted">
                            <small>Before we proceed, please provide your personal information.</small>
                        </p>
                        <asp:TextBox ID="txtfullname" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Fullname" />

                        <asp:TextBox ID="txtaddress" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Complete Address" />

                        <asp:TextBox TextMode="Phone" ID="txtcontact" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Contact Number" />

                        <div class=" row">
                            <div class="col-md-6">
                                <asp:TextBox ID="txtbday" runat="server" CssClass="form-control mb-3 modern-date" BorderWidth="1px"/>
                            </div>
                            <div class="col-md-6">
                                 <asp:TextBox ID="txtnationality" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Nationality" />
                            </div>
                            <div class="col-md-6">
                                <asp:DropDownList CssClass="form-select" ID="ddlgender" runat="server">
                                    <asp:ListItem Value="" Text="--Select gender--" />
                                    <asp:ListItem Text="Male" />
                                    <asp:ListItem Text="Female" />
                                </asp:DropDownList>
                            </div>
                            <div class="col-md-6">
                                 <asp:TextBox ID="txtage" TextMode="Number" runat="server" CssClass="form-control mb-3" BorderWidth="1px" placeholder="Age" />
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>
                                <asp:CheckBox ID="checkbox" runat="server" /> <small>By clicking this box, your privacy and security are our top priorities. We assure you that all personal information provided will be kept strictly confidential and handled with the utmost care. It will be securely stored and used solely for the purpose outlined in our agreement, in compliance with relevant data protection regulations.</small></span>
                        </div>
                        <asp:Button Text="Submit" CssClass="btn btn-primary w-100 mt-2" runat="server" ID="btnsubmit" OnClick="btnsubmit_Click" />
                    </div>
                </div>
                    </div>
                <asp:Label Text="" ID="lblid" runat="server" />

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
    <script language="javascript" type="text/javascript">
         $(document).ready(function () {
             $('#<%=error.ClientID%>').fadeOut(5000, function () {
                $(this).html("");
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        // Get the input element
        var dateInput = document.querySelector(".modern-date");

        // Get today's date in YYYY-MM-DD format for max attribute
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
        var yyyy = today.getFullYear();
        var todayFormatted = yyyy + '-' + mm + '-' + dd;

        // Set attributes
        dateInput.type = "text"; // Start as text to show placeholder
        dateInput.placeholder = "Birthday";
        dateInput.setAttribute("max", todayFormatted); // Set max date to today

        // Add focus event to change type to date when clicked
        dateInput.addEventListener("focus", function () {
            this.type = "date";
        });

        // Add blur event to change back if empty
        dateInput.addEventListener("blur", function () {
            if (this.value === "") {
                this.type = "text";
            }
        });

        // Add additional validation to prevent future dates (in case max doesn't work in some browsers)
        dateInput.addEventListener("change", function () {
            var selectedDate = new Date(this.value);
            var currentDate = new Date();

            // Clear value if future date is selected
            if (selectedDate > currentDate) {
                this.value = "";
                alert("Please select a date in the past for birthdate");
            }
        });
    });
</script>
</body>

</html>
