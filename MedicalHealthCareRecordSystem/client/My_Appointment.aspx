<%@ Page Title="My Appointment | Medical Apointment System" Language="C#" MasterPageFile="~/ClientDashboard.master" AutoEventWireup="true" CodeFile="My_Appointment.aspx.cs" Inherits="_Default" %>
<%@ MasterType VirtualPath="~/ClientDashboard.master" %>
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
    <h5 class="mb-2 text-titlecase mb-4">My Appointment</h5>
    <asp:UpdatePanel runat="server">
        <ContentTemplate>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h4 class="card-title">My Appointment List</h4>
                                    <asp:LinkButton CssClass="btn btn-primary btn-sm" runat="server" OnClick="btnnew_Click" ID="btnnew">New Appointment</asp:LinkButton>
                                </div>
                                <div class="table-responsive">
                                     <asp:GridView ID="myappointmentlist" DataKeyNames="id" runat="server" CssClass="datatable table-hover table table-striped" AutoGenerateColumns="false">
                                    <Columns>
                                        <asp:BoundField DataField="id" HeaderText="#"></asp:BoundField>
                                        <asp:BoundField DataField="DOCTOR" HeaderText="Doctor Name"></asp:BoundField>
                                        <asp:BoundField DataField="TIME" HeaderText="Appointment Time"></asp:BoundField>
                                        <asp:BoundField DataField="DATE" HeaderText="Appointment Date"></asp:BoundField>
                                        <asp:BoundField DataField="TYPE" HeaderText="Appointment Type"></asp:BoundField>
                                        <asp:TemplateField HeaderText="Status">
                                            <ItemTemplate>
                                                <asp:Label ID="lblstatus" runat="server" Text='<%#Eval("STATUS") %>'
                                                    CssClass='<%# GetStatusCssClass(Eval("STATUS").ToString()) %>'></asp:Label>
                                            </ItemTemplate>
                                        </asp:TemplateField>
                                        <asp:TemplateField>
                                            <ItemTemplate>
                                                <asp:LinkButton ID="linkdelete" Visible='<%# Eval("STATUS").ToString() == "Pending" %>' OnClick="linkdelete_Click" OnClientClick="return confirm('Are you sure want to delete this appointment? Click OK to continue.');" CssClass="text-danger" runat="server"><i class="fa fa-trash-can fa-xl"></i></asp:LinkButton>
                                            </ItemTemplate>
                                        </asp:TemplateField>
                                    </Columns>
                                </asp:GridView>
                                </div>
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
            <asp:PostBackTrigger ControlID="btnnew" />
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

