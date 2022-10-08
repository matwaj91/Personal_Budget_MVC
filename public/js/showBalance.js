var sumOfIncome = parseFloat(document.getElementById("sumOfIncome").innerText);
var sumOfExpense = parseFloat(document.getElementById("sumOfExpense").innerText);
	
if(sumOfIncome > sumOfExpense)
{
	document.getElementById("showBalance").innerHTML = "Great job! You manage your finances very well!";
	document.getElementById("showBalance").style.color = "#74aa54";
}
else if(sumOfIncome < sumOfExpense)
{
	document.getElementById("showBalance").innerHTML = "Be careful! You spend more than you earn.";
	document.getElementById("showBalance").style.color = "rgb(241, 82, 82)";
}
