<%@ Page Title="Doctor List of Appointment | Medical Apointment System" Language="C#" MasterPageFile="~/DoctorDashboard.master" AutoEventWireup="true" CodeFile="MyAppointment.aspx.cs" Inherits="_Default" ValidateRequest="false" %>
<%@ MasterType VirtualPath="~/DoctorDashboard.master" %>
<asp:Content ID="Content1" ContentPlaceHolderID="ContentPlaceHolder1" Runat="Server">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <asp:ScriptManager runat="server" />
    <style>
        /*CUSTOM CSS FOR CKEDITOR*/

     .ck.ck-editor__main>.ck-editor__editable{
          height: 250px;
          margin-bottom: 20px;
     }
      .large-checkbox input {
            width: 17px;
            height: 17px;
            cursor: pointer;
        }
      .ck-file-dialog-button{
          display:none;
      }
    </style>
     <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
   <script type="text/javascript">
       function initializeCKEditor() {
           ClassicEditor
               .create(document.querySelector('#<%= txtremarks.ClientID %>'), {
                   htmlSupport: {
                       allow: [
                           {
                               name: 'p',
                               attributes: true,
                               classes: true,
                               styles: true
                           },
                           {
                               name: 'strong',
                               attributes: true,
                               classes: true,
                               styles: true
                           }
                           // Add other allowed tags as needed
                       ]
                   }
               })
               .catch(error => console.error(error));
    }

    $(document).ready(function () {
        initializeCKEditor();

        // Reinitialize CKEditor on UpdatePanel updates
        Sys.WebForms.PageRequestManager.getInstance().add_endRequest(function () {
            initializeCKEditor();
        });
    });

    // Initialize CKEditor on modal show
    $('#<%= panel1.ClientID %>').on('shown.bs.modal', function () {
           initializeCKEditor();
       });
   </script>

    <h5 class="mb-2 text-titlecase mb-4"><span class=" text-muted">My Appointment</span> / List</h5>
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
                                    <asp:GridView DataKeyNames="id" ID="grvpending" runat="server" CssClass="datatable table-hover table table-striped" AutoGenerateColumns="false">
                                    <Columns>
                                        <asp:BoundField DataField="PATIENT_ID" HeaderText="#"></asp:BoundField>
                                        <asp:BoundField DataField="PATIENT_NAME" HeaderText="Patient Name"></asp:BoundField>
                                        <asp:BoundField DataField="TIME" HeaderText="Appointment Time"></asp:BoundField>
                                        <asp:BoundField DataField="DATE" HeaderText="Appointment Date"></asp:BoundField>
                                        <asp:BoundField DataField="TYPE" HeaderText="Appointment Type"></asp:BoundField>
                                         <asp:TemplateField HeaderText="Status">
                                        <ItemTemplate>
                                        <asp:Label ID="lblstatus" runat="server" Text='<%#Eval("STATUS") %>'  CssClass='<%# GetStatusCssClass(Eval("STATUS").ToString()) %> '></asp:Label>
                                        </ItemTemplate>
                                    </asp:TemplateField>
                                       <%--<asp:TemplateField ItemStyle-CssClass="d-none">
                                        <ItemTemplate>
                                            <p><%# Truncate(Eval("MESSAGE").ToString(), 20) %></p>
                                        </ItemTemplate>
                                    </asp:TemplateField>--%>
                                        <asp:TemplateField>
                                            <ItemTemplate>
                                                <asp:LinkButton ID="btnmodal" ToolTip="Remarks" OnClick="btnmodal_Click" runat="server"><i class="fa-regular fa-message fa-lg"></i></asp:LinkButton>
                                            </ItemTemplate>
                                        </asp:TemplateField>
                                        <asp:TemplateField>
                                            <ItemTemplate>
                                                <asp:LinkButton ID="btncompleted" OnClick="btncompleted_Click" OnClientClick="return confirm('Are you sure you want to complete this appointment? Click OK to proceed.');" Text="Set as complete" ToolTip="Apply as completed" CssClass="btn btn-xs btn-success" runat="server"></asp:LinkButton>
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
                                <h4 class="modal-title" id="myModalLabel">Follow up</h4>
                                <asp:LinkButton CssClass=" btn-close" runat="server" ID="btnclose" aria-label="Close" OnClick="btnclose_Click"></asp:LinkButton>
                            </div>
                            <div class="modal-body bg-white">
                                <small class="text-muted pb-4">Your remarks here</small>
                                <br />
                                <div class="row">
                                    <div class="col-lg-8">
                                        <asp:TextBox runat="server" ID="txtremarks" placeholder="Add remarks here." TextMode="MultiLine" Height="280px" CssClass="form-control"  />
                                    </div>
                                    <div class="col-lg-4">
                                        <span>
                                            <asp:CheckBox CssClass="large-checkbox" runat="server" ID="chkfollowup" onclick="toggleTextBox()"/>
                                            <asp:Label Text=" Follow up check up" runat="server" />
                                        </span>
                                        <asp:TextBox style="display: none;" runat="server" TextMode="Date" ID="txtfollowdate" CssClass="form-control mt-3" />
                                    </div>
                                </div>
                            </div>
                            <div style="background: #fff;" class="modal-footer">
                                        <asp:Label ID="lblidvisible" Visible="false" runat="server" />
                                        <asp:Label ID="lblvpatientName" Visible="false" runat="server" />
                                        <asp:Label ID="lblvdoctorName" Visible="false" runat="server" />
                                        <asp:Label ID="lblvtime" Visible="false" runat="server" />
                                        <asp:Label ID="lblvdate" Visible="false" runat="server" />
                                        <asp:Label ID="lblvtype" Visible="false" runat="server" />
                                        <asp:Label ID="lbldates" Visible="false" runat="server" />
                                        <asp:Label ID="lblvpatientId" Visible="false" runat="server" />
                                        <asp:Label ID="lblvdoctorId" Visible="false" runat="server" />
                                <asp:Button Text="Update follow up" CssClass="btn btn-primary" OnClick="btnapprove_Click" ID="btnapprove" runat="server" />
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
            <asp:PostBackTrigger ControlID="btnapprove"/>
            <asp:AsyncPostBackTrigger ControlID="btnclose" />
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
    <script type="text/javascript">
        function toggleTextBox() {
            var checkBox = document.getElementById('<%= chkfollowup.ClientID %>');
        var textBox = document.getElementById('<%= txtfollowdate.ClientID %>');

            if (checkBox.checked) {
                textBox.style.display = "inline";
            } else {
                textBox.style.display = "none";
            }
        }
</script>
</asp:Content>

