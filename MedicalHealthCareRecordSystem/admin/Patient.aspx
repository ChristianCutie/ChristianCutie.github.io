<%@ Page Title="Patient List | Medical Apointment System" Language="C#" MasterPageFile="~/AdminDashboard.master" AutoEventWireup="true" CodeFile="Patient.aspx.cs" Inherits="Patient" %>
<%@ MasterType VirtualPath="~/AdminDashboard.master" %>
<asp:Content ID="Content1" ContentPlaceHolderID="ContentPlaceHolder1" Runat="Server">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <asp:ScriptManager runat="server" />
    <style>
        /*CUSTOM CSS FOR MODAL*/
      
          .ModalPopupBG {
            background-color: black;
            filter: alpha(opacity=50);
            opacity: 0.8;
        }

        .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
    </style>
    <h5 class="mb-2 text-titlecase mb-4"><span class=" text-muted">Accounts</span> / Patient</h5>
    <asp:UpdatePanel runat="server">
        <ContentTemplate>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h4 class="card-title">Patient List</h4>
                                </div>
                                <asp:GridView ID="patientlist" runat="server" CssClass="datatable table-hover table table-striped" AutoGenerateColumns="false">
                                    <Columns>
                                        <asp:BoundField DataField="FULLNAME" HeaderText="Full Name"></asp:BoundField>
                                        <asp:BoundField DataField="ADDRESS" HeaderText="Address"></asp:BoundField>
                                        <asp:BoundField DataField="CONTACT" HeaderText="Contact"></asp:BoundField>
                                        <asp:BoundField DataField="BDAY" HeaderText="Birthday"></asp:BoundField>
                                        <asp:BoundField DataField="NATIONALITY" HeaderText="Nationality"></asp:BoundField>
                                        <asp:BoundField DataField="GENDER" HeaderText="Gender"></asp:BoundField>
                                        <asp:BoundField DataField="AGE" HeaderText="Age"></asp:BoundField>
                                        <asp:TemplateField>
                                            <ItemTemplate>
                                                <asp:LinkButton Visible="false" CssClass="btn btn-secondary btn-rounded btn-xs" runat="server"><i class="fa fa-edit"></i></asp:LinkButton>
                                                <asp:LinkButton Visible="false" CssClass="btn btn-danger btn-xs btn-rounded" runat="server"><i class="fa fa-trash-can"></i></asp:LinkButton>
                                            </ItemTemplate>
                                        </asp:TemplateField>
                                    </Columns>
                                </asp:GridView>
                            </div>
                        </div>
                    </div>
                </div>
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

