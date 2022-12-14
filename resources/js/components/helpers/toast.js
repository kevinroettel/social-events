import { Toast } from "bootstrap";

export function toast(bodyText, toastType) {
    const toastElement = document.getElementById('notification-toast');
    const toast = new Toast(toastElement);
    
    if (toastType == 'success') toastElement.classList.add('bg-success');
    else if (toastType == 'warning') toastElement.classList.add('bg-warning');
    else if (toastType == 'error') toastElement.classList.add('bg-danger');
    
    toastElement.children[0].children[0].innerHTML = bodyText;
    toast.show();
}