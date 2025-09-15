function resetModal() {
    document.getElementById("modalTitle").innerText = "Agregar Evento";
    document.getElementById("eventForm").reset();
    document.getElementById("eventId").value = "";
    document.getElementById("deleteBtn").style.display = "none";
    document.getElementById("formErrors")?.classList.add("d-none");
    document.getElementById("removeTalleristaBtn")?.classList.add("d-none");
}

function initCalendar() {
    let calendarEl = document.getElementById("calendar");
    let modalEl = document.getElementById("eventModal");
    let form = document.getElementById("eventForm");
    let deleteBtn = document.getElementById("deleteBtn");
    let errorBox = document.getElementById("formErrors");

    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        editable: true,
        selectable: true,
        events: "/agenda/events", // ✅ carga vía AJAX

        dateClick: function (info) {
            resetModal();
            document.getElementById("fecha").value = info.dateStr;
            new bootstrap.Modal(modalEl).show();
        },

        eventClick: function (info) {
            resetModal();
            document.getElementById("modalTitle").innerText = "Editar Evento";
            document.getElementById("eventId").value = info.event.id;

            let start = info.event.start;
            document.getElementById("fecha").value = start
                .toISOString()
                .split("T")[0];
            document.getElementById("hora").value = start
                .toTimeString()
                .slice(0, 5);

            document.getElementById("colegio_id").value =
                info.event.extendedProps.colegio_id;
            document.getElementById("taller_id").value =
                info.event.extendedProps.taller_id;

            // 🔹 Manejo de talleristas con restricción por rol
            let userRole = document.querySelector(
                'meta[name="user-role"]'
            ).content;
            let userId = parseInt(
                document.querySelector('meta[name="user-id"]').content
            );

            let talleristasSelect = document.getElementById("talleristas");

            Array.from(talleristasSelect.options).forEach((o) => {
                let valueInt = parseInt(o.value);
                o.selected =
                    info.event.extendedProps.talleristas.includes(valueInt);

                // 🔒 Restricción: si es tallerista, solo puede tocar su propio option
                if (userRole === "tallerista" && valueInt !== userId) {
                    o.disabled = true;
                } else {
                    o.disabled = false;
                }
            });

            // 🔹 Botón remover (solo visible si es tallerista)
            let removeBtn = document.getElementById("removeTalleristaBtn");
            if (removeBtn) {
                if (userRole === "tallerista") {
                    removeBtn.style.display = "inline-block";
                    removeBtn.onclick = function () {
                        Array.from(talleristasSelect.options).forEach((o) => {
                            if (parseInt(o.value) === userId) {
                                o.selected = false;
                            }
                        });
                    };
                } else {
                    removeBtn.style.display = "none";
                }
            }

            deleteBtn.style.display = "inline-block";
            deleteBtn.onclick = function () {
                if (confirm("¿Seguro que deseas eliminar este evento?")) {
                    fetch(`/agenda/${info.event.id}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                    })
                        .then((res) => res.json())
                        .then((data) => {
                            if (data.success) {
                                info.event.remove();
                                bootstrap.Modal.getInstance(modalEl).hide();
                            } else {
                                alert("Error al eliminar");
                            }
                        })
                        .catch(() => alert("Error de conexión"));
                }
            };

            new bootstrap.Modal(modalEl).show();
        },
    });

    calendar.render();

    // ✅ Guardar evento con AJAX
    form.onsubmit = function (e) {
        e.preventDefault();

        let talleristasSelect = document.getElementById("talleristas");
        if (talleristasSelect.selectedOptions.length > 2) {
            errorBox.textContent =
                "Solo puedes seleccionar hasta 2 talleristas.";
            errorBox.classList.remove("d-none");
            return;
        }

        let formData = new FormData(form);
        formData.append(
            "_token",
            document.querySelector('meta[name="csrf-token"]').content
        );

        fetch("/agenda/store", {
            method: "POST",
            body: formData,
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    calendar.refetchEvents(); // refresca sin recargar
                    bootstrap.Modal.getInstance(modalEl).hide();
                } else {
                    errorBox.textContent = "Error al guardar el evento.";
                    errorBox.classList.remove("d-none");
                }
            })
            .catch(() => {
                errorBox.textContent = "Error de conexión.";
                errorBox.classList.remove("d-none");
            });
    };
}
