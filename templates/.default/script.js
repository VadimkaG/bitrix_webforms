BX.ready(()=>{
	var forms = document.querySelectorAll("form[webshop-webform-ajax]");
	if (forms && forms.length > 0 && webshopWebFormAjaxOnce !== true)
	forms.forEach(item=>{
		BX.Event.bindOnce(item,"submit",e=>{
			e.preventDefault();
			var props = {};
			for (let element of e.target.elements) {
				if (element.type !== "submit")
					props[element.name] = element.value;
			}
			props["wsform_event"] = e.target.getAttribute("webshop-webform-ajax");
			props["AJAX"] = "Y";
			const request = new XMLHttpRequest();
			request.open('POST', window.location.pathname);
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			var query = "";
			for (var key in props) {
				if (query !== "") query += "&";
				query += key + "=" + encodeURIComponent(props[key]);
			}
			request.onreadystatechange = function() {
				if (request.readyState != 4) return;
				if (this.status == 200) {
					if (request.responseText.length > 1)
						e.target.outerHTML = request.responseText;
				} else
					alert("Ошибка отправки сообщения: ["+this.status+"] "+this.statusText);
			};
			request.send(query);
		});
	});
	var webshopWebFormAjaxOnce = true;
});