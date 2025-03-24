<%@ Page Title="History | Medical Apointment System" Language="C#" MasterPageFile="~/DoctorDashboard.master" AutoEventWireup="true" CodeFile="~/doctor/History.aspx.cs" Inherits="History" %>
<%@ MasterType VirtualPath="~/DoctorDashboard.master" %>
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
    <h5 class="mb-2 text-titlecase mb-4">History</h5>
    <asp:UpdatePanel runat="server">
        <ContentTemplate>
            <div class="row">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <h4 class="card-title">Appointment History</h4>
                                <%--<asp:LinkButton CssClass="btn btn-primary btn-sm" runat="server" OnClick="btnnew_Click" ID="btnnew">Appointment History</asp:LinkButton>--%>
                            </div>
                            <div class="table-responsive">
                                <asp:GridView ID="myappointmentlist" DataKeyNames="DOCTOR_ID" runat="server" CssClass="datatable table-hover table table-striped" AutoGenerateColumns="false">
                                    <Columns>
                                        <asp:BoundField DataField="DOCTOR_ID" HeaderText="#"></asp:BoundField>
                                        <asp:BoundField DataField="PATIENT_NAME" HeaderText="Patient Name"></asp:BoundField>
                                        <asp:BoundField DataField="SPECIALTY" HeaderText="Appointment Type"></asp:BoundField>
                                        <asp:TemplateField HeaderText="Status">
                                            <ItemTemplate>
                                                <asp:Label ID="lblstatus" runat="server" Text='<%#Eval("STATUS") %>'
                                                    CssClass='<%# GetStatusCssClass(Eval("STATUS").ToString()) %>'></asp:Label>
                                            </ItemTemplate>
                                        </asp:TemplateField>
                                        <asp:TemplateField>
                                            <ItemTemplate>
                                                <asp:LinkButton ToolTip="View" ID="linkView" OnClick="linkView_Click" Text="View" CssClass="btn btn-primary btn-sm float-end me-2" runat="server"></asp:LinkButton>
                                            </ItemTemplate>
                                        </asp:TemplateField>
                                    </Columns>
                                </asp:GridView>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Over all</h3>
                                <hr />
                                <div class="overflow-auto" style="height: 50rem;">
                                     <asp:Repeater ID="rptOverAllHistory" runat="server">
                                    <ItemTemplate>
                                        <div class="border rounded p-3 mb-2">
                                    <h3 class="card-title">
                                        <div class=" d-flex justify-content-between">
                                            <asp:Label Text='<%#Eval("PATIENT_NAME") %>' runat="server" />
                                            <asp:Label Text='<%#Eval("STATUS") %>' CssClass='<%# GetStatusCssClass(Eval("STATUS").ToString()) %>' runat="server" />
                                        </div>
                                    </h3>
                                    <asp:Label Text='<%#Eval("SPECIALTY") %>' CssClass="text-muted font-italic" runat="server" /><br />
                                    <asp:Label Text='<%#Eval("TIME") %>' CssClass="text-muted" runat="server" /><br />
                                    <asp:Label Text='<%#Eval("DATE") %>' CssClass="text-muted mb-3" runat="server" />
                                    <br />
                                    <p>
                                        <asp:Label Text='<%#Eval("DATE_ADDED") %>' CssClass="float-end" runat="server" />
                                    </p>

                                </div>
                                    </ItemTemplate>
                                </asp:Repeater>
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

