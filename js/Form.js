class Form {
    constructor(divId, data) {
        this.divId = divId;
        this.data = data;
    }

    showDiv(divId) {
        document.getElementById('show').innerHTML = `<div id = ${divId}></div>`;
    }
    insertTemplate(divId, data) {
        document.getElementById('show').innerHTML = null;
        this.showDiv(divId);

        const markup = new Markup();
        markup.show(divId);

        switch (divId) {
            case 'auth':
                const auth = new Auth();
                auth.render();
                break;
            case 'signup':
                const signup = new Signup();
                signup.render();
                break;
            //case 'menu':
            //    const menu = new Menu(data);
            //    menu.render();
            //    break;
            case 'game':
                const game = new Game(data);
                game.render();
                break;
            case 'mapList':
                const mapList = new MapList(data);
                mapList.render();
                break;
        }

    }
}
