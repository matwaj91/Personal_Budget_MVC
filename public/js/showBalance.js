var sumOfIncome = parseFloat(document.getElementById("sumOfIncome").innerText);
var sumOfExpense = parseFloat(document.getElementById("sumOfExpense").innerText);

var balance = sumOfIncome - sumOfExpense;

if ((sumOfIncome === 0.00) && (sumOfExpense === 0.00)) {

	document.getElementById("showBalance").innerHTML = "There are neither incomes nor expenses at a given time!";
	document.getElementById("showBalance").style.color = "black";
}
else if (sumOfIncome >= sumOfExpense) {

	document.getElementById("showBalance").innerHTML = `Balance: ${balance}  Great job! You manage your finances very well!`;
	document.getElementById("showBalance").style.color = "#74aa54";
}
else if (sumOfIncome < sumOfExpense) {

	document.getElementById("showBalance").innerHTML = `Balance: ${balance}   Be careful! You spend more than you earn.`;
	document.getElementById("showBalance").style.color = "rgb(241, 82, 82)";
}
