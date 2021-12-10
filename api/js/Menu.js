class Menu {
    constructor(userData) {
        this.divId = 'menu';
        this.userData = userData;
    }

    funcsBtn() {
        const form = new Form();
        let info = this.userData;
        if (info) {
            async function logout(token) {
                await fetch(
                    `api/?method=logout&token=${token}`
                );
            }

            const logOutBtn = document.getElementById('logOutBtn');
            const mapListBtn = document.getElementById('mapListBtn');

            mapListBtn.addEventListener('click', function () {
                const mapList = new MapList();
                form.insertTemplate(mapList.divId);
            })

            logOutBtn.addEventListener('click', async function () {
                console.log(info)
                if (info) {
                    await logout(info.token);
                }
                info = null;
                const auth = new Auth();
                form.insertTemplate(auth.divId);
            });
        }

    }


    render() {
        const menuDiv = document.getElementById(`${this.divId}`);
        if (menuDiv) {
            this.funcsBtn();
        }
    }
}