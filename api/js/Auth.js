class Auth {
    constructor() {
        this.divId = 'auth';
    }

    funcsBtn() {
        const output = document.getElementById('output');
        const loginInput = document.getElementById('login');
        const loginBtn = document.getElementById('loginBtn');
        const passwordInput = document.getElementById('password');
        const signupBtn = document.getElementById('signupBtn');
        const form = new Form();

        async function getLoginAndPassword(login, password) {
            const answer = await fetch(
                `api/?method=login&login=${login}&password=${password}`
            );
            return await answer.json();
        }

        signupBtn.addEventListener('click', function () {
            const signup = new Signup();
            form.insertTemplate(signup.divId);
        });

        loginBtn.addEventListener('click', async function () {
            let login = loginInput.value;
            let password = passwordInput.value;
            let answer = await getLoginAndPassword(login, password);
            if (answer['data'].token) {
                const menu = new Menu();
                form.insertTemplate(menu.divId, answer['data']);
                localStorage.setItem('token', answer['data'].token);
            } else output.innerHTML = 'Введены неверные авторизационные данные!';
        });
    }

    render() {
        const authDiv = document.getElementById(`${this.divId}`);
        if (authDiv) {
            //this.authElem();
            this.funcsBtn();
        }
    }
}