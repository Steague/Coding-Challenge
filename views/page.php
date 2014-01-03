<html>
<head>
<title> Task 1 </title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="http://jqueryui.com/resources/demos/style.css" />
<style>
table td,th
{
        text-align:center;
}
</style>
<script>
$(function()
{
    $("#datepicker").datepicker();
    $("#showdata").click(function()
    {
        $.ajax({
                url: "task1.php",
                data: {
                        ajax: 1,
                        date: $("#datepicker").val()
                },
                beforeSend: function()
                {
                        $("#showdata").val("Refreshing...").attr("disabled", "disabled");
                }
        }).done(function(data)
        {
                if (data == "no data")
                {
                        $("#datatable").text("No data to display.");
                }
                else
                {
                        $("#datatable").html("<table><tr><th>Min Temp.</th><th>Max Temp.</th><th>Avg. Temp.</th></t r><tr><td>"+data.min+"</td><td>"+data.max+"</td><td>"+data.avg+"</td></tr></table>");
                }
                $("#showdata").val("Refresh").removeAttr("disabled");
        });
    });
    $(document).ready(function()
    {
        $("#showdata").click();
    });
});
</script>
</head>
<body>

<p>Date: (Showing yesterday by default) <input type="text" id="datepicker" name="date" value="<?php echo $date;?>"  /><input type="button" id="showdata" value="Refresh" /></p>

<p id="datatable"></p>

</body>
</html>