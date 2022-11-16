let categoryField = document.getElementById("category");
let dateField = document.getElementById("date");
let amountField = document.getElementById("amount");

categoryField.addEventListener('change', async () => {

    const category = categoryField.options[categoryField.selectedIndex].value;
    const date = dateField.value;
    const amount = amountField.value;

    if (!category) {

        hideBalance();
    }

    checkExpenseBalance(category, date, amount);
})

dateField.addEventListener('change', async () => {

    const category = categoryField.options[categoryField.selectedIndex].value;
    const date = dateField.value;
    const amount = amountField.value;

    checkExpenseBalance(category, date, amount);
})

amountField.addEventListener('change', async () => {

    const category = categoryField.options[categoryField.selectedIndex].value;
    const date = dateField.value;
    const amount = amountField.value;

    checkExpenseBalance(category, date, amount);
})

const getLimitForCategory = async (category) => {

    try {
        const res = await fetch(`/expense/categoryLimit/${category}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log('ERROR', e);
    }
}

const getSumOfExpensesForSelectedCategory = async (category, date) => {

    try {
        const res = await fetch(`/expense/sumOfExpenses/${category}/${date}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log('ERROR', e);
    }
}

const renderOnDOM = async (intLimitAmount, intSumOfExpenses, differenceLimitSumOfExpenses, sumProvidedAmountExpenses) => {

    document.getElementById("box").style.visibility = "visible";

    document.getElementById("limit").innerHTML = `Limit: ${intLimitAmount}$`;
    document.getElementById("expenses").innerHTML = `Sum of Expenses: ${intSumOfExpenses}$`;
    document.getElementById("diff").innerHTML = `Difference: ${differenceLimitSumOfExpenses}$`;
    document.getElementById("sum").innerHTML = `Amount + Sum: ${sumProvidedAmountExpenses}$`

    if (intLimitAmount >= sumProvidedAmountExpenses) {

        document.getElementById("box").style.color = " #85bb65";
    }
    else if (intLimitAmount < sumProvidedAmountExpenses) {

        document.getElementById("box").style.color = "rgb(241, 82, 82)";
    }
}

const displayExpenseBalance = async (amount, sumOfExpenses, limitAmount) => {

    var intLimitAmount = parseFloat(limitAmount);
    if (sumOfExpenses === null) {

        sumOfExpenses = 0;
    }
    console.log(sumOfExpenses);
    var intSumOfExpenses = parseFloat(sumOfExpenses);
    var intAmount = parseFloat(amount);

    var differenceLimitSumOfExpenses = intLimitAmount - intSumOfExpenses;
    var sumProvidedAmountExpenses = intAmount + intSumOfExpenses;

    renderOnDOM(intLimitAmount, intSumOfExpenses, differenceLimitSumOfExpenses, sumProvidedAmountExpenses);
}

const checkExpenseBalance = async (category, date, amount) => {

    if (!!category) {

        const limitAmount = await getLimitForCategory(category);

        if (limitAmount == null) {

            hideBalance();
        }

        if (!!date) {

            const sumOfExpenses = await getSumOfExpensesForSelectedCategory(category, date);

            if (!!amount && !!limitAmount) {

                displayExpenseBalance(amount, sumOfExpenses, limitAmount);
            }
        }
    }
}

const hideBalance = () => {

    document.getElementById("box").style.visibility = "hidden";
}



