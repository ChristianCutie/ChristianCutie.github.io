<%@ Page Title="" Language="C#" MasterPageFile="~/AdminDashboard.master" AutoEventWireup="true" CodeFile="Appointment.aspx.cs" Inherits="_Default" %>
<%@ MasterType VirtualPath="~/AdminDashboard.master" %>
<asp:Content ID="Content1" ContentPlaceHolderID="ContentPlaceHolder1" Runat="Server">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <asp:ScriptManager runat="server" />
    <h5 class="mb-2 text-titlecase mb-4">Appointment</h5>
    <asp:UpdatePanel runat="server">
        <ContentTemplate>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <h4 class="card-title">List of Appointment</h4>
                            </div>
                            <div class="table-responsive">
                                <asp:GridView ID="doctorlistappt" DataKeyNames="id" runat="server" CssClass="table datatable table-hover table-striped" AutoGenerateColumns="false">
                                    <Columns>
                                        <asp:BoundField DataField="id" HeaderText="#"></asp:BoundField>
                                        <asp:BoundField DataField="PATIENT_NAME" HeaderText="Patient Name"></asp:BoundField>
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
                                        <%--<asp:BoundField DataField="MESSAGE" HeaderText="Remarks"></asp:BoundField>--%>
                                        <asp:TemplateField>
                                            <ItemTemplate>
                                                <asp:LinkButton ID="btnmodal" OnClick="btnmodal_Click" runat="server"><i class="fa fa-eye fa-lg"></i></asp:LinkButton>
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
                            <h4 class="modal-title" id="myModalLabel">Patient Information</h4>
                            <asp:LinkButton CssClass="btn-close" runat="server" ID="btnclose" aria-label="Close" OnClick="btnclose_Click"></asp:LinkButton>
                        </div>
                        <div class="modal-body bg-white">
                            <div class="row">
                                <div class="col-lg-6">
                                    <asp:Label ID="lblidvisible" Visible="false" runat="server" />
                                    <asp:Label ID="lblPatientId" Visible="false" runat="server" />
                                    <asp:Label ID="lblDoctorId" Visible="false" runat="server" />
                                    <asp:Label ID="lbldocName" Visible="false" runat="server" />
                                    <table class="table  table-padding">
                                        <tr class="pt-4 pb-4">
                                            <th scope="row">PATIENT NAME: </th>
                                            <td>
                                                <asp:Label ID="lblpname" Text="" CssClass=" form-label" runat="server" /></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">TIME: </th>
                                            <td>
                                                <asp:Label ID="lbltime" Text="" CssClass=" form-label" runat="server" /></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">DATE: </th>
                                            <td>
                                                <asp:Label ID="lbldate" Text="" CssClass=" form-label" runat="server" /></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">TYPE: </th>
                                            <td>
                                                <asp:Label ID="lbltype" Text="" CssClass=" form-label" runat="server" /></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">STATUS: </th>
                                            <td>
                                                <asp:Label ID="lblstatus" Text="" CssClass=" form-label" runat="server" /></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-lg-6">
                                    <asp:TextBox runat="server" ID="txtmessage" Enabled="false" placeholder="Patient message here." TextMode="MultiLine" Height="193px" CssClass="form-control" />
                                </div>
                            </div>
                        </div>
                        <div style="background: #fff;" class="modal-footer">
                            <asp:Button OnClientClick="return confirm('Are you sure you want to APPROVE this appointment? Click OK to proceed.');" Text="Approve" CssClass="btn btn-primary" OnClick="btnapprove_Click" ID="btnapprove" runat="server" />
                            <asp:Button OnClientClick="return confirm('Are you sure you want to CANCEL this appointment? Click OK to proceed.');" Text="Cancel" CssClass="btn btn-danger" OnClick="btndecline_Click" ID="btndecline" runat="server" />
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
        </ContentTemplate>
        <Triggers>
            <asp:AsyncPostBackTrigger ControlID="btnclose" />
            <asp:PostBackTrigger ControlID="btnapprove" />
            <asp:PostBackTrigger ControlID="btndecline" />
        </Triggers>
    </asp:UpdatePanel>
</asp:Content>

