<%@ Page Title="Add new Appointment | Medical Apointment System" Language="C#" MasterPageFile="~/ClientDashboard.master" AutoEventWireup="true" CodeFile="Add_new.aspx.cs" Inherits="_Default" %>
<%@ MasterType VirtualPath="~/ClientDashboard.master" %>
<asp:Content ID="Content1" ContentPlaceHolderID="ContentPlaceHolder1" Runat="Server">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        .table th, .table td {
            vertical-align: text-top !important;
        }

        .no-underline-calendar a {
            text-decoration: none; /* Removes underline */
        }
        /* Optional: Add hover effect if desired */
        .no-underline-calendar a:hover {
            text-decoration: underline; /* Adds underline back on hover */
        }
    </style>
    <asp:ScriptManager runat="server" />
    <h5 class="mb-2 text-titlecase mb-4"><span class="text-muted">My Appointment</span> / Add New Appointment</h5>
    <asp:UpdatePanel ID="up1" runat="server">
        <ContentTemplate>
            <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <asp:Label Text="" ID="lblid" Visible="false" runat="server" />
                    <h4 class="card-title">New Appointment</h4>
                    <asp:Label Text="Doctor *" CssClass="form-label fw-bold" runat="server" />
                    <asp:DropDownList style="height: 45px" ID="ddldoctor" AutoPostBack="true" OnSelectedIndexChanged="ddldoctor_SelectedIndexChanged" CssClass=" form-select mb-4" runat="server">
                        
                    </asp:DropDownList>

                    <asp:Label Text="Specialty *" CssClass="form-label fw-bold" runat="server" />
                    <asp:DropDownList style="height: 45px" ID="ddltype" CssClass="form-select mb-4" runat="server" AutoPostBack="true" OnSelectedIndexChanged="ddltype_SelectedIndexChanged">

                    </asp:DropDownList>
                    <div class="row">
                        <div class="col-lg-8">
                             <asp:Label Text="Appointment Time *" CssClass="form-label fw-bold" runat="server" />
                            <asp:DropDownList runat="server" ID="ddltime" CssClass="form-select mb-4" Style="height: 45px" AutoPostBack="true" OnSelectedIndexChanged="ddltime_SelectedIndexChanged">
                            </asp:DropDownList>
                        </div>
                        <div class="col-lg-4">
                             <asp:Label Text="Appointment Date *" CssClass="form-label fw-bold" runat="server" />
                            <asp:TextBox ID="txtdate" TextMode="Date" runat="server" CssClass="form-control mb-3" BorderWidth="1px" />
                        </div>
                    </div>
                    <%--<asp:Label Text="Remarks" CssClass="form-label fw-bold" runat="server" />
                   <asp:TextBox runat="server" ID="txtmessage" TextMode="MultiLine" CssClass="form-control mb-4" style="height: 100px" placeholder="Feel free to add any additional notes or special requests here."></asp:TextBox>--%>

                    <asp:Button Text="Submit Appointment" runat="server" OnClientClick="return confirm('Are you sure you want to create this account? Click YES to proceed.');" CssClass="btn btn-primary" ID="btnsubmit" OnClick="btnsubmit_Click" />
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <nav class="pe-0 ps-0">
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Calendar</button>
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">List</button>
                    </div>
                </nav>
                <div class="tab-content pe-0 ps-0" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="card mb-3 border-top-0">
                            <div class="card-body">
                                <h4 class="card-title">Appointment Schedule Calendar</h4>
                                <asp:Calendar ID="cal1" OnDayRender="cal1_DayRender" runat="server" CssClass="no-underline-calendar"
                                    BackColor="White" BorderColor="White" Font-Names="Inter" Font-Size="9pt" ForeColor="Black"
                                    Height="250px" NextPrevFormat="CustomText" Width="100%" BorderWidth="1px">
                                    <DayHeaderStyle Font-Bold="True" Font-Size="10pt" ForeColor="#000" CssClass="text-center" />
                                    <NextPrevStyle Font-Size="12pt" ForeColor="#333333" VerticalAlign="Bottom" Font-Underline="false" />
                                    <OtherMonthDayStyle ForeColor="#999999" />
                                    <SelectedDayStyle BackColor="#333399" ForeColor="White" />
                                    <TitleStyle BackColor="White" Font-Bold="True" Font-Size="12pt" ForeColor="#333399" BorderColor="Black" />
                                    <TodayDayStyle BackColor="White" BorderColor="#999999" BorderWidth="1pt" />
                                </asp:Calendar>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <div style="height:250px" class="card border-top-0 mb-3">
                            <div class="card-body">
                                <h4 class="card-title">Appointment Schedule List</h4>
                                <asp:Label Text="" ID="lblListViewVisible" runat="server" />
                                <asp:Repeater ID="rptList" runat="server">
                                    <HeaderTemplate>
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                    </HeaderTemplate>

                                    <ItemTemplate>
                                        <tr>
                                            <td><%# Eval("DATE") %></td>
                                            <td><%# Eval("TIME") %></td>
                                        </tr>
                                    </ItemTemplate>
                                    <FooterTemplate>
                                        </tbody>
                                      </table>
                                    </FooterTemplate>
                                </asp:Repeater>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Doctor's Schedule & Information</h4>
                        <table class="table">
                            <tr>
                                <th scope="row"><span class="fw-bold">Name:</span></th>
                                <td> <asp:Label ID="txtdocname" Text="" CssClass="form-label" runat="server" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><span class="fw-bold">Degree:</span></th>
                                <td> <asp:Label ID="txtdocdegree" Text="" runat="server" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><span class="fw-bold">Time Available:</span></th>
                                <td> <asp:Label ID="txtdoctime" Text="" runat="server" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><span class="fw-bold">Day Available:</span></th>
                                <td> 
                                   <asp:Repeater ID="rptday" runat="server">
                                        <ItemTemplate>
                                            <div class="d-flex flex-column">
                                                <asp:Label Text='<%#Eval("DAY") %>' CssClass="lh-1 p-1" runat="server" />
                                            </div>
                                        </ItemTemplate>
                                    </asp:Repeater>
                                    </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </ContentTemplate>
        <Triggers>
            <asp:PostBackTrigger ControlID="btnsubmit" />
             <asp:AsyncPostBackTrigger ControlID="ddldoctor" EventName="SelectedIndexChanged" />
        </Triggers>
    </asp:UpdatePanel>
</asp:Content>

