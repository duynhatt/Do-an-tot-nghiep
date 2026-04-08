/**
 * Toast nhẹ (dùng class .custom-toast trong client/layout/header.blade.php).
 * type: 'success' | 'error' | 'warning'
 */
window.showClientToast = function (message, type) {
    if (message === undefined || message === null || String(message).trim() === '') {
        return;
    }
    type = type === 'error' ? 'error' : (type === 'warning' ? 'warning' : 'success');

    const el = document.createElement('div');
    el.className = 'custom-toast ' + type;
    el.setAttribute('role', 'alert');
    el.style.whiteSpace = 'pre-wrap';
    el.textContent = message;
    document.body.appendChild(el);

    const ms = type === 'error' ? 5200 : 4000;
    setTimeout(function () {
        el.classList.add('fade-out');
        function cleanup() {
            el.removeEventListener('animationend', cleanup);
            if (el.parentNode) {
                el.remove();
            }
        }
        el.addEventListener('animationend', cleanup);
        setTimeout(cleanup, 700);
    }, ms);
};
