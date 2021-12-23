window.onload = async function () {
    async function getPlan() {
        const answer = await fetch(
            `api/?method=checkCookie`
        );
        return await answer.json();
    }

    async function isArrivalReady() {
        const answer = await fetch(
            `api/?method=isArrivalReady`
        );
        return await answer.json();
    }

    async function start() {
        const form = new Form();
        let answer = await getPlan();
        if (answer) {
            if (answer['data'].token) {
                const mapList = new MapList();
                form.insertTemplate(mapList.divId, answer['data']);
            } else {
                const auth = new Auth();
                form.insertTemplate(auth.divId);
            }
        }
        //setInterval(isArrivalReady(), 100);
    }

    //setInterval(isArrivalReady(), 100);
    
    start();
}