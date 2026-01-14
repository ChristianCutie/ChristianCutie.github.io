<%@ Page Language="C#" AutoEventWireup="true" CodeFile="Default2.aspx.cs" Inherits="Default2" %>
<%@ Register Src="~/ChatControl.ascx" TagName="ChatControl" TagPrefix="uc1" %>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>Time Slot Selection</title>
    <script type="text/javascript">
        function validateTimeRange() {
            var timeIn = parseInt(document.getElementById('<%= ddlTimeIn.ClientID %>').value);
            var timeOut = parseInt(document.getElementById('<%= ddlTimeOut.ClientID %>').value);

            if (timeOut <= timeIn) {
                alert('Time Out must be after Time In');
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <form id="form1" runat="server">
        <div>
            <h2>Select Time Range</h2>
            
            <div>
                <label>Date ID (for demo purposes):</label>
                <asp:TextBox ID="txtDateID" runat="server" Text="1"></asp:TextBox>
            </div>
            
            <div>
                <label>Time In:</label>
                <asp:DropDownList ID="ddlTimeIn" runat="server"></asp:DropDownList>
            </div>
            
            <div>
                <label>Time Out:</label>
                <asp:DropDownList ID="ddlTimeOut" runat="server"></asp:DropDownList>
            </div>
            
            <div>
                <asp:Button ID="btnSaveIntervals" runat="server" Text="Generate & Save Time Slots" 
                    OnClientClick="return validateTimeRange();" OnClick="btnSaveIntervals_Click" />
            </div>
            
            <div>
                <asp:Label ID="lblMessage" runat="server" ForeColor="Green"></asp:Label>
                <asp:Label ID="lblError" runat="server" ForeColor="Red"></asp:Label>
            </div>
            
            <h3>Generated Time Slots:</h3>
            <asp:ListBox ID="lstTimeSlots" runat="server" Height="200px" Width="200px"></asp:ListBox>
        </div>
    </form>
</body>
</html>
