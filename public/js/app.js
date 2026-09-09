document.addEventListener('submit', event => {
    const message = event.target.dataset.confirm;
    if (message && !window.confirm(message)) event.preventDefault();
});
document.querySelectorAll('[data-print]').forEach(button => button.addEventListener('click', () => window.print()));
document.querySelectorAll('[data-weather]').forEach(async element => {
    try {
        const response = await fetch(element.dataset.weather, {headers: {Accept: 'application/json'}});
        if (!response.ok) throw new Error('Vrijeme nije dostupno');
        const data = await response.json();
        element.textContent = `${data.temperature} °C · ${data.description} · vjetar ${data.wind} km/h`;
    } catch (error) { element.textContent = 'Vremenski podaci trenutačno nisu dostupni.'; }
});
