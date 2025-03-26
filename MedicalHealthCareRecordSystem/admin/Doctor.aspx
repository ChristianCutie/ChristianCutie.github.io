<%@ Page Title="Doctor List | Medical Apointment System" Language="C#" MasterPageFile="~/AdminDashboard.master" AutoEventWireup="true" CodeFile="Doctor.aspx.cs" Inherits="_Default" %>
<%@ MasterType VirtualPath="~/AdminDashboard.master" %>
<asp:Content ID="Content1" ContentPlaceHolderID="ContentPlaceHolder1" Runat="Server">
    <link href="/admin/css/custom.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
     <script type="text/javascript">
         function validateTimeRange() {
             var timeIn = parseInt(document.getElementById('<%= ddltimein.ClientID %>').value);
            var timeOut = parseInt(document.getElementById('<%= ddltimeout.ClientID %>').value);

             if (timeOut <= timeIn) {
                 alert('Time Out must be after Time In');
                 return false;
             }
             return true;
         }
    </script>
    <asp:ScriptManager runat="server" />
    <h5 class="mb-2 text-titlecase mb-4"><span class=" text-muted">Accounts</span> / Doctor</h5>
    <asp:UpdatePanel runat="server">
        <ContentTemplate>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h4 class="card-title">Doctor List</h4>
                                    <asp:LinkButton CssClass="btn btn-primary btn-sm" runat="server" OnClick="btnadd_Click" ID="btnadd">Add New Doctor</asp:LinkButton>
                                </div>
                                <div class="table-responsive">
                                     <asp:GridView ID="doctorlist" runat="server" DataKeyNames="id" CssClass="datatable table-hover table table-striped" AutoGenerateColumns="false" OnRowDataBound="doctorlist_RowDataBound">
                                    <Columns>
                                        <asp:BoundField DataField="id" HeaderText="#"></asp:BoundField>
                                        <asp:BoundField DataField="FULLNAME" HeaderText="Full Name"></asp:BoundField>
                                        <asp:BoundField DataField="DEGREE" HeaderText="Specialty"></asp:BoundField>
                                        <asp:BoundField DataField="AVAILABLE_TIME" HeaderText="Available Time"></asp:BoundField>
                                        <asp:BoundField DataField="EMAIL_ADDRESS" HeaderText="Email Address"></asp:BoundField>
                                         <asp:TemplateField HeaderText="Password">
                                        <ItemTemplate>
                                        <asp:Label ID="lblPassword" runat="server" Text='<%#Eval("PASSWORD") %>'></asp:Label>
                                        </ItemTemplate>
                                    </asp:TemplateField>
                                        <asp:TemplateField HeaderText="Status">
                                            <ItemTemplate>
                                                <asp:Label ID="lblstatus" runat="server" Text='<%#Eval("STATUS") %>'
                                                    CssClass='<%# GetStatusCssClass(Eval("STATUS").ToString()) %>'></asp:Label>
                                            </ItemTemplate>
                                        </asp:TemplateField>
                                        <asp:TemplateField>
                                            <ItemTemplate>
                                                <asp:LinkButton Visible="false" CssClass="btn btn-secondary btn-rounded btn-xs" runat="server"><i class="fa fa-edit"></i></asp:LinkButton>
                                                <asp:LinkButton Visible="false" CssClass="btn btn-danger btn-xs btn-rounded" ID="linkdelete" OnClick="linkdelete_Click" OnClientClick="return confirm('Are you sure want to delete this account? Click OK to continue.');" runat="server"><i class="fa fa-trash-can"></i></asp:LinkButton>
                                            </ItemTemplate>
                                        </asp:TemplateField>
                                    </Columns>
                                </asp:GridView>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <asp:Panel ID="panel1" runat="server" CssClass="container-fluid ">
                    <div class=" modal-dialog modal-lg bg-white rounded-3 ">
                        <div class="modal-content">
                            <div style="border: none;" class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Create new account</h4>
                                <asp:LinkButton CssClass=" btn-close" runat="server" ID="btnclose" aria-label="Close" OnClick="btnclose_Click"></asp:LinkButton>
                            </div>
                            <div class="modal-body bg-white p-4">
                                <small class="text-muted pb-4">Please fill up the form  below to get started</small>
                                <br />
                                <div class="row">
                                    <div class="col-lg-6">
                                        <asp:Label Text="FULLNAME *" CssClass="form-label fw-bold" runat="server" />
                                        <asp:RequiredFieldValidator ValidationGroup="grp2" ErrorMessage="required" Display="Dynamic" ForeColor="#DC4637" ControlToValidate="txtfullname" runat="server" />
                                        <asp:TextBox ID="txtfullname" runat="server" CssClass="form-control mb-3" BorderWidth="1px" />


                                        <asp:Label Text="SPECIALTY *" CssClass="form-label fw-bold" runat="server" />
                                        <asp:RequiredFieldValidator ValidationGroup="grp2" ErrorMessage="required" Display="Dynamic" ForeColor="#DC4637" ControlToValidate="ddlspecialty" runat="server" />
                                        <asp:DropDownList Style="height: 45px" ID="ddlspecialty" CssClass="form-select mb-4" runat="server">
                                            <asp:ListItem Value="" Text="--What is the doctor's specialty?--" />
                                            <asp:ListItem Text="Surgery" />
                                            <asp:ListItem Text="Diagnostic Tests" />
                                            <asp:ListItem Text="Vaccination" />
                                            <asp:ListItem Text="Mental Health Evaluation" />
                                            <asp:ListItem Text="Specialist Referral" />
                                            <asp:ListItem Text="Pre-Op Consultation" />
                                            <asp:ListItem Text="Post-Op Care" />
                                            <asp:ListItem Text="Chronic Disease Management" />
                                            <asp:ListItem Text="Nutrition Counseling" />
                                            <asp:ListItem Text="Laboratory Review" />
                                        </asp:DropDownList>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <asp:Label Text="AVAILABLE DAY *" CssClass="form-label fw-bold" runat="server" />
                                                <asp:CustomValidator ValidationGroup="grp2" ID="cvCheckboxGroup" runat="server" ErrorMessage="Please select at least 5 options." ForeColor="Red" OnServerValidate="cvCheckboxGroup_ServerValidate"> </asp:CustomValidator>

                                            </div>
                                            <div class="col-lg-6">
                                                <span>
                                                    <asp:CheckBox ID="chkMonday" runat="server" />
                                                    Monday</span>
                                                <br />
                                                <span>
                                                    <asp:CheckBox ID="chkTuesday" runat="server" />
                                                    Tuesday</span>
                                                <br />
                                                <span>
                                                    <asp:CheckBox ID="chkWednesday" runat="server" />
                                                    Wednesday</span>
                                                <br />
                                                <span>
                                                    <asp:CheckBox ID="chkThursday" runat="server" />
                                                    Thursday</span>
                                                <br />
                                                <span>
                                                    <asp:CheckBox ID="chkFriday" runat="server" />
                                                    Friday</span>
                                                <br />
                                                <span>
                                                    <asp:CheckBox ID="chkSaturday" runat="server" />
                                                    Satruday</span>
                                                <br />
                                                <span>
                                                    <asp:CheckBox ID="chkSunday" runat="server" />
                                                    Sunday</span>
                                                <br />
                                            </div>
                                        </div>
                                        <asp:Label Text="AVAILABLE TIME *" CssClass="form-label fw-bold" runat="server" />
                                        <div class="d-flex justify-content-around">
                                            <asp:DropDownList ID="ddltimein" CssClass="form-select" runat="server">
                                            </asp:DropDownList>
                                            <asp:RequiredFieldValidator ValidationGroup="grp2" ErrorMessage="required" ForeColor="#DC4637" Display="Dynamic" ControlToValidate="ddltimein" runat="server" />
                                            - 
                                            <asp:DropDownList CssClass="form-select" ID="ddltimeout" runat="server">
                                            </asp:DropDownList>
                                            <asp:RequiredFieldValidator ValidationGroup="grp2" ErrorMessage="required" ForeColor="#DC4637" Display="Dynamic" ControlToValidate="ddltimeout" runat="server" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <asp:Label Text="EMAIL ADDRESS *" CssClass="form-label fw-bold" runat="server" />
                                        <asp:RequiredFieldValidator ValidationGroup="grp2" ErrorMessage="required" ForeColor="#DC4637" Display="Dynamic" ControlToValidate="txtemail" runat="server" />
                                        <asp:TextBox ID="txtemail" runat="server" CssClass="form-control mb-3" BorderWidth="1px" />

                                        <asp:Label Text="PASSWORD *" CssClass="form-label fw-bold" runat="server" /><asp:RequiredFieldValidator ValidationGroup="grp2" ErrorMessage="required" ForeColor="#DC4637" Display="Dynamic" ControlToValidate="txtpassword" runat="server" />
                                        <asp:TextBox TextMode="Password" ID="txtpassword" runat="server" CssClass="form-control mb-3" BorderWidth="1px" />

                                        <asp:Label Text="CONFIRM PASSWORD *" CssClass="form-label fw-bold" runat="server" />
                                        <asp:TextBox TextMode="Password" ID="txtconfirmpassword" runat="server" CssClass="form-control mb-3" BorderWidth="1px" />
                                        <asp:CompareValidator Display="Dynamic" ForeColor="#DC4637" ErrorMessage="Password does not match" ControlToValidate="txtconfirmpassword" ControlToCompare="txtpassword" runat="server" />
                                        <asp:Button ValidationGroup="grp2" OnClientClick="return validateTimeRange() && confirm('Are you sure you want to create this account? Click OK to proceed.');"  Text="Create" CssClass="btn btn-primary" runat="server" ID="btnsubmit" OnClick="btnsubmit_Click" />
                                    </div>
                                </div>
                            </div>
                            <div style="background: #fff;" class="modal-footer">
                                <asp:Label ID="lblId2" runat="server" />
                                <asp:Label ID="lblerror2" runat="server" />
                            </div>
                        </div>
                    </div>
                </asp:Panel>
                <asp:HiddenField ID="HiddenField1" runat="server" />
                <ajaxToolkit:ModalPopupExtender ID="modal1" runat="server" TargetControlID="HiddenField1" PopupControlID="panel1" DropShadow="false"
                    BackgroundCssClass="ModalPopupBG">
                    <Animations>
                        <OnShown>
                          <FadeIn duration="0.2" Fps="100" />
                        </OnShown>
                    </Animations>
                </ajaxToolkit:ModalPopupExtender>
                 <div runat="server" id="error" visible="false">
                    <div class=" position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                        <div class=" toast show bg-danger ">
                            <div class="toast-body">
                                <asp:Label ID="lblerror" runat="server" />
                            </div>
                        </div>
                    </div>
                </div>
                 <div runat="server" id="success" visible="false">
                    <div class=" position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                        <div class=" toast show bg-success ">
                            <div class="toast-body">
                                <asp:Label ID="lblsuccess" runat="server" />
                            </div>
                        </div>
                    </div>
                </div>
        </ContentTemplate>
        <Triggers>
            <asp:PostBackTrigger ControlID="btnadd" />
            <asp:AsyncPostBackTrigger ControlID="btnclose" />
            <asp:PostBackTrigger ControlID="btnsubmit" />
        </Triggers>
    </asp:UpdatePanel>
    <script language="javascript" type="text/javascript">
         $(document).ready(function () {
             $('#<%=error.ClientID%>').fadeOut(5000, function () {
                $(this).html("");
            });
        });
    </script> <script language="javascript" type="text/javascript">
                  $(document).ready(function () {
                      $('#<%=success.ClientID%>').fadeOut(5000, function () {
                 $(this).html("");
             });
         });
    </script>
</asp:Content>

