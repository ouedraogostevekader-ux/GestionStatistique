const input = document.getElementById('no-paste');
['copy', 'cut', 'paste', 'drop'].forEach(evt =>
    input.addEventListener(evt, e => {
        e.preventDefault();
    })
);
