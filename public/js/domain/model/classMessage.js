class Message {
    constructor(content = '', type = 'info', duration = 3000) {
        this.content = content;
        this.type = type;
        this.duration = duration;
        this.element = null;
        this.visible = false;
    }

    setText(content) {
        this.content = content;
        if (this.element) {
            this.element.textContent = content;
        }
    }

    show() {
       
        if (this.visible === false) {
            
            this.visible = true;
            this.element = document.createElement('div');
            this.element.classList.add('message', this.type);
            this.element.textContent = this.content;

            document.body.appendChild(this.element);


            setTimeout(() => {
                this.element.classList.add('show');
            }, 10);


            setTimeout(() => {
                this.hide();
            }, this.duration);
        }
    }

    hide() {
        if (!this.element) return;

        this.element.classList.remove('show');

        setTimeout(() => {
            this.element.remove();
            this.element = null;
            this.visible = false;
        }, 300);
    }
}

export { Message };