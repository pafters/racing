class MapList {
    constructor() {
        this.divId = 'mapList';
        this.token = localStorage.getItem('token');
    }

    joinArrival() {
        var countClick = 0;
        //тут будет написан запрос в бэк на подключение к комнате и выход из нее
        document.addEventListener('click', function (e) { //тут мы получаем id комнаты для "подключения" к ней

            if (!e.target.classList.contains('roomDiv')) {
                return
            } else {
                const elements = document.getElementsByClassName('entered');
                if (elements.length == 1) {
                    document.getElementById(`${e.target.id}`).classList.remove('entered');
                    if (elements[0]) {
                        if (e.target.id != elements[0].id) {
                            console.log(3)
                            document.getElementById(`${elements[0].id}`).classList.remove('entered');
                            document.getElementById(`${e.target.id}`).classList.add('entered');
                        }
                    }

                } else if (elements.length < 1) {
                    document.getElementById(`${e.target.id}`).classList.add('entered');
                }
            }



        });
    }

    rooms() {
        const menuBtn = document.getElementById('menuBtn');
        const rooms = document.getElementById('rooms');
        const checkRoomsBtn = document.getElementById('checkRoomsBtn');
        const createRoomBtn = document.getElementById('createRoomBtn');

        const form = new Form();
        roomsListUpdate(this.token);

        async function getUser() {
            const answer = await fetch(
                `api/?method=checkCookie`
            );
            return await answer.json();
        }

        async function getAllRooms(token) {
            const answer = await fetch(
                `api/?method=getAllRooms&token=${token}`
            );
            return await answer.json();
        }

        async function createRoom(token, roomName) { //тут отправляется запрос в бэк с названием комнаты от пользака
            const answer = await fetch(
                `api/?method=createRoom&token=${token}&name=${roomName}`
            );
            return await answer.json();
        }
        //setInterval(roomsListUpdate, 5000);

        async function roomsListUpdate(token) { //эта штука обновляет список 
            rooms.innerHTML = null;
            let answer = await getAllRooms(token); //получаем список комнат
            if (answer.data) {
                const roomList = answer.data;
                for (let i = 0; i < roomList.length; i++) {
                    let div = document.createElement('div');
                    div.id = `room${i}`;
                    div.classList.add('roomDiv');
                    document.getElementById('rooms').appendChild(div);
                    div.innerHTML = roomList[i].name; //тут должно начинаться заполнение блока комнаты (имя, статус, кол. игроков, название трассы)
                }
            }
        }

        checkRoomsBtn.addEventListener('click', async () => { //обновление комнат на кнопку
            roomsListUpdate(this.token);
        });

        menuBtn.addEventListener('click', async function () { //возврат в меню
            let answer = await getUser();
            if (answer) {
                const menu = new Menu();
                form.insertTemplate(menu.divId, answer['data']);
            }
        });

        createRoomBtn.addEventListener('click', async () => { //кнопка создания комнаты
            let roomName = document.getElementById('roomNameInp').value;
            await createRoom(this.token, roomName);
        });
    }

    render() {
        const mapListDiv = document.getElementById(this.divId);
        if (mapListDiv) {
            this.rooms();
            this.joinArrival();
        }
    }
}