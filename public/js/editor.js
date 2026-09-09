if (window.Quill) {
    const input = document.getElementById('description');
    const element = document.getElementById('editor');
    element.hidden = false;
    const editor = new Quill(element, {theme:'snow',modules:{toolbar:[[{header:[2,3,false]}],['bold','italic','underline'],[{list:'ordered'},{list:'bullet'}],['clean']]}});
    // Opis se ponovno čisti na serveru; editor nije sigurnosna provjera.
    editor.clipboard.dangerouslyPasteHTML(input.value);
    input.hidden = true;
    input.required = false;
    document.getElementById('offer-form').addEventListener('submit', () => { input.value = editor.root.innerHTML; });
}
