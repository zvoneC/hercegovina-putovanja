const catalog = document.getElementById('catalog-app');
if (catalog && window.Vue) {
    const initialHtml = catalog.querySelector('[data-results]').innerHTML;
    const initialCount = Number(catalog.querySelector('.results-bar strong').textContent);
    catalog.querySelector('[data-results]').innerHTML = '';
    catalog.querySelector('.results-bar strong').textContent = '';
    Vue.createApp({
        data() { return { loading: false, error: '', initialHtml, count: initialCount }; },
        methods: {
            async search() {
                if (this.loading) return;
                this.loading = true;
                this.error = '';
                const params = new URLSearchParams(new FormData(this.$refs.filters));
                const url = catalog.dataset.url + '?' + params.toString();
                try {
                    const response = await fetch(url, { headers: { Accept: 'application/json' } });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Pretraga nije uspjela.');
                    // Kartice renderira Blade, pa su uneseni nazivi već HTML-escaped.
                    this.$refs.results.innerHTML = data.html;
                    this.count = data.count;
                    history.replaceState(null, '', url + '#ponuda');
                } catch (error) { this.error = 'Ponude nisu učitane. Provjerite filtre i pokušajte ponovno.'; }
                finally { this.loading = false; }
            }
        }
    }).mount(catalog);
}
