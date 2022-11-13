const renderOnDOM = () => {

    //renderDom if necessary (wyswietlamy informacje jesli jest ustawiony limit)
    //document.getElementById("demo").innerHTML = x;*/ // przyda nam sie do wyrenderowania informacji na samym koncu
};

const calculateLimits = () => {

    //pobieramy kwote ktora wpisujemy
    //calculate Limits (obliczamy juz czy wiecej user wydal czy mniej od ustalonego limitu)
};

const getSumOfExpensesForSelectedMonth = () => {

    fetch(`/sumForSelectedMonth/:${id}?date=${date}`);
}

const getLimitForCategory = () => { //sprawdza limit dla kategorri z danym id

    fetch(`/categoryLimit/:${id}`);
};

const checkLimit = () => {

    // musimy sobie pobrac date jaka mamy ustawiona
    getSumOfExpensesForSelectedMonth();
    calculateLimits();
    renderOnDOM();
};

const checkCategory = () => { // przy pomocy onChange pobieramy dana kategorie

    let x = document.getElementById("category").value;
    getLimitForCategory();
    checkLimit();
};
