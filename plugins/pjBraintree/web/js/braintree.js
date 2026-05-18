(function (window, document) {
	function Demo(config){
		this.config = config;
		this.config.development = config.development || false;
	 
		this.paymentForm = document.getElementById(config.formID);
		this.inputs = document.querySelectorAll("input[type=text], input[type=email], input[type=tel]");
		this.button = this.paymentForm.querySelector(".button");
	
		this.states = {
			"show" : "active",
			"wait" : "loading"
		};
		this.focusClass = "has-focus";
		this.valueClass = "has-value";
	
		this.initialize();
	}
	
	Demo.prototype.initialize = function(){
		var that = this;
	
		this.events();
		[].forEach.call(this.inputs, function(element) {
			that.labelHandler(element);
		});
		//this.notify("error");
	};

	Demo.prototype.events = function() {
		var that = this;
	
		[].forEach.call(this.inputs, function(element) {
			element.addEventListener("focus", function() {
                var label = document.querySelector("label[for='" + this.id + "']");
                if (label) {
                    label.classList.add(that.focusClass);
                }
                that.labelHandler(this);
			});
            element.addEventListener("keydown", function() {
                that.labelHandler(this);
            });
            element.addEventListener("blur", function() {
                var label = document.querySelector("label[for='" + this.id + "']");
                if (label) {
                    label.classList.remove(that.focusClass);
                }
                that.labelHandler(this);
            });
		});
	};

	Demo.prototype.labelHandler = function(element) {
		var that = this;
		var input = element;
		var label = document.querySelector("label[for='" + element.id + "']");
	
		window.setTimeout(function() {
			var hasValue = input.value.toString().length > 0;
	
			if (hasValue) {
				label.classList.add(that.valueClass);
			} else {
				label.classList.remove(that.valueClass);
			}
		}, 10);
	};

	Demo.prototype.notify = function(status) {
		var that = this;
		var notice = document.querySelector(".notice-" + status);
		var delay = (this.config.development === true) ? 4000 : 2000;
	
		notice.style.display = "block";
	
		window.setTimeout(function() {
			notice.classList.add("show");
			that.button.classList.remove(that.states.wait);
		
			window.setTimeout(function() {
				notice.classList.remove("show");
				window.setTimeout(function() {
					notice.style.display = "none";
				}, 310);
			}, delay);
		}, 10);
	};

	function Modal(element) {
		if (!(this instanceof Modal)) {
			return new Modal(element);
		}

		this.backdrop = null;

		if (element instanceof HTMLElement) {
            this.element = element;
		} else if (typeof element === "string") {
            this.element = document.querySelector(element);
		} else {
			throw new TypeError("Invalid element.");
		}

		if (!(element instanceof HTMLElement)) {
            throw new Error("Modal element not found.");
		}
	}

	Modal.prototype = (function() {

		function show() {
            this.element.dispatchEvent(new Event("show"));

            var that = this;
            [].forEach.call(this.element.querySelectorAll(".mb-close"), function(el) {
            	el.addEventListener("click", function(event) {
                    event.preventDefault();
            		that.hide.call(that);
				}, {
            		once: true
				});
			});

            this.backdrop = document.createElement("div");
            this.backdrop.className = "mb-backdrop in";
            var body = document.body;
            body.appendChild(this.backdrop);
            body.classList.add("mb-open");

            this.element.classList.add("in");

			this.element.dispatchEvent(new Event("shown"));
		}

		function hide() {
            this.element.dispatchEvent(new Event("hide"));

            this.element.classList.remove("in");
            document.body.classList.remove("mb-open");

            if (this.backdrop && this.backdrop.parentNode) {
            	this.backdrop.parentNode.removeChild(this.backdrop);
			}

            this.element.dispatchEvent(new Event("hidden"));
		}

		return {
			hide: hide,
			show: show
		};
	})();

    window.BraintreeDemo = Demo;
    window.BraintreeModal = Modal;

})(window, document);