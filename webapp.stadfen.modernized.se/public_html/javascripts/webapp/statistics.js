var StatisticsPage =
{
	AccountID : null,
	Year : null,
	Month : null,
	BarChart : null,
	
	Load : function()
	{
		var $tablebody = $("#statistics_accountlist_table tbody");
		var accID = StatisticsPage.AccountID;
		var year = StatisticsPage.Year;
		var month = StatisticsPage.Month;
		
		var monthNames = ["Januari", "Februari", "Mars", "April", "Maj", "Juni",
  "Juli", "Augusti", "September", "Oktober", "November", "December"
];
		
		document.getElementById('statistics_current_month').innerHTML = monthNames[month-1] + ", " + year;
		
		document.getElementById('statistics_btn_allaccounts').style.display = (accID==0 ? "none" : "");
		
		$tablebody.html("");
		
		ComSystem.Request("statisticspage",
		{
			acc : accID,
			y : year,
			m : month,
		}, function(data,data2)
		{
			//Verify still same
			if(StatisticsPage.AccountID == accID && StatisticsPage.Year == year && StatisticsPage.Month == month)
			{
				var nameLabel = 
				(
					StatisticsPage.AccountID == 0
						? "Alla konton"
						: 	(
								data.CurrentAccountDisplayname == null
								? data.CurrentAccountEmailAddress
								: ( data.CurrentAccountDisplayname + "("+ data.CurrentAccountEmailAddress +")" )
						  	)	
						
				);
				document.getElementById('statistics_currentuser').innerHTML = nameLabel;
				for(var i = 0; i < data.AccountList.length; i++)
				{
					$tablebody.append('<tr><td>'+(data.AccountList[i].DisplayName ? data.AccountList[i].DisplayName : "<i>Inget</i>")+'</td><td>'+data.AccountList[i].EmailAddress+'</td><td class="right"><button class="btn btn-default" onclick="StatisticsPage.NavigateAccount('+ data.AccountList[i].ID +');return false;">Visa statistik</button></td></tr>');
				}
				
				StatisticsPage.UpdateChart(data.Outgoing);
			}
		});
	},
	
	NavigateNextMonth : function()
	{
		var newMonth = (StatisticsPage.Month == 12 ? 1 : StatisticsPage.Month+1);
		var newYear = (newMonth == 1 ? StatisticsPage.Year+1 : StatisticsPage.Year);
		
		StatisticsPage.NavigateMonth(newYear,newMonth);
	},
	NavigatePrevMonth : function()
	{
		var newMonth = (StatisticsPage.Month == 1 ? 12 : StatisticsPage.Month-1);
		var newYear = (newMonth == 12 ? StatisticsPage.Year-1 : StatisticsPage.Year);
		
		StatisticsPage.NavigateMonth(newYear,newMonth);
	},
	
	NavigateMonth : function(y,m)
	{
		NavigationSystem.Navigate("module_statistics y"+ y + " m"+m + ((StatisticsPage.AccountID != null && StatisticsPage.AccountID != 0) ? " account account" + StatisticsPage.AccountID : ""));
	},
	
	NavigateAccount : function(acc)
	{
		NavigationSystem.Navigate("module_statistics y"+ StatisticsPage.Year + " m" + StatisticsPage.Month + " account account" + acc);
	},
	
	UpdateChart : function(data)
	{
		var labelsArray = new Array();
		var messageArray = new Array();
		for(var i = 0; i < data.length; i++)
		{
			labelsArray[i] = data[i].SendDate;
			messageArray[i] = data[i].MessageCount;
		}
		var data = {
		labels: labelsArray,
		datasets: [
				{
					label: "Statistik",
					fillColor: "#97e8f9",
					strokeColor: "#1bd3f9",
					pointColor: "#1bd3f9",
					data: messageArray
				}
			]
		};
		
		if (SettingsFunc.BarChart == null)
		{
			var parent = $('#bar-chart-statistics').parent();
			$('#bar-chart-statistics').remove();
			parent.append('<canvas id="bar-chart-statistics" height="60"></canvas>');
			var canvas = document.getElementById('bar-chart-statistics')
			var ctx = canvas.getContext("2d");
			SettingsFunc.BarChart = new Chart(ctx).Line(data, { scaleShowGridLines : false, showScale : false, scaleFontSize: 12, responsive: true, scaleFontColor: "#7297a1", scaleLineColor: "#7297a1" });
		}
		else
		{
			var parent = $('#bar-chart-statistics').parent();
			$('#bar-chart-statistics').remove();
			parent.append('<canvas id="bar-chart-statistics" height="60"></canvas>');
			
			var canvas = document.getElementById('bar-chart-statistics')
			var ctx = canvas.getContext("2d");
			
			SettingsFunc.BarChart = new Chart(ctx).Line(data, { scaleShowGridLines : false, showScale : false, scaleFontSize: 12, responsive: true, scaleFontColor: "#7297a1", scaleLineColor: "#7297a1" });
			
		}
		
		SettingsFunc.BarChart.removeData();
		SettingsFunc.BarChart.addData(data);
	}
};