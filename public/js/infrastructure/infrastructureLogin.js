import { Message } from '../domain/model/classMessage.js';


const MESSAGE = new Message();

async function checkLogin() {
    const USER_INPUT = document.querySelector('input[name="usuario"]').value;
    const PASS_INPUT = document.querySelector('input[name="contrasenya"]').value;

    try {
        const RESPONSE = await fetch('/login/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                usuario: USER_INPUT,
                contrasenya: PASS_INPUT
            })
        });

        const DATA = await RESPONSE.json();
 
        if (DATA.success) {
            console.log('Login correcto');
            window.location.href = '/home';
        } else {
            console.log(DATA.message);
            MESSAGE.setText('Error al iniciar Sesión');
            MESSAGE.show();
        }

    } catch (error) {
        console.error('Error:', error);
        MESSAGE.setText('Error de conexión al Servidor');
        MESSAGE.show();
    }
}





document.querySelector('form').addEventListener('submit', (e) => {
    e.preventDefault();
    checkLogin();
});