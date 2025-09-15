document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");
    const modalEl = document.getElementById("eventModal");
    const form = document.getElementById("eventForm");
    const deleteBtn = document.getElementById("deleteBtn");
    const errorBox = document.getElementById("formErrors");

    // 🔹 Reset modal
    function resetModal() {
        document.getElementById("modalTitle").innerText = "Agregar Evento";
        form.reset();
        document.getElementById("eventId").value = "";
        errorBox.classList.add("d-none");
        deleteBtn.style.display = "none";

        document
            .getElementById("talleristaSelectContainer")
            ?.classList.remove("d-none");
        document.getElementById("talleristaResumen")?.classList.add("d-none");
        document.getElementById("t1Nombre").innerText = "";
        document.getElementById("t2Nombre").innerText = "";
    }

    // 🔹 Calendar init
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        editable: true,
        selectable: true,
        events: "/agenda/events",

        dateClick: function (info) {
            resetModal();
            document.getElementById("fecha").value = info.dateStr;
            new bootstrap.Modal(modalEl).show();
        },

        eventClick: function (info) {
            resetModal();
            document.getElementById("modalTitle").innerText = "Editar Evento";
            document.getElementById("eventId").value = info.event.id;

            const start = info.event.start;
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

            const talleristas = info.event.extendedProps.talleristas || [];
            const userRole = document.querySelector(
                'meta[name="user-role"]'
            ).content;

            if (["admin", "supervisor"].includes(userRole)) {
                document.getElementById("tallerista1").value =
                    talleristas[0] || "";
                document.getElementById("tallerista2").value =
                    talleristas[1] || "";
                if (talleristas.length > 0) mostrarResumen();
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
                            } else alert("Error al eliminar");
                        })
                        .catch(() => alert("Error de conexión"));
                }
            };

            new bootstrap.Modal(modalEl).show();
        },
    });

    calendar.render();

    // 🔹 Evita duplicados en selects
    function syncTalleristaOptions() {
        const t1 = document.getElementById("tallerista1");
        const t2 = document.getElementById("tallerista2");
        const val1 = t1.value;
        const val2 = t2.value;

        Array.from(t1.options).forEach(
            (o) => (o.disabled = o.value && o.value === val2)
        );
        Array.from(t2.options).forEach(
            (o) => (o.disabled = o.value && o.value === val1)
        );
    }

    // 🔹 Mostrar resumen
    function mostrarResumen() {
        const t1 = document.getElementById("tallerista1");
        const t2 = document.getElementById("tallerista2");

        if ((t1 && t1.value) || (t2 && t2.value)) {
            document
                .getElementById("talleristaSelectContainer")
                .classList.add("d-none");
            document
                .getElementById("talleristaResumen")
                .classList.remove("d-none");
            document.getElementById("t1Nombre").innerText =
                t1.selectedOptions[0]?.text || "No asignado";
            document.getElementById("t2Nombre").innerText =
                t2.selectedOptions[0]?.text || "No asignado";
        }
    }

    document
        .getElementById("editarTalleristasBtn")
        ?.addEventListener("click", function () {
            document
                .getElementById("talleristaSelectContainer")
                .classList.remove("d-none");
            document
                .getElementById("talleristaResumen")
                .classList.add("d-none");
        });

    document
        .getElementById("tallerista1")
        ?.addEventListener("change", syncTalleristaOptions);
    document
        .getElementById("tallerista2")
        ?.addEventListener("change", syncTalleristaOptions);

    // 🔹 Guardar evento
    form.onsubmit = function (e) {
        e.preventDefault();

        const talleristas = [];
        const t1 = document.getElementById("tallerista1");
        const t2 = document.getElementById("tallerista2");
        if (t1 && t1.value) talleristas.push(t1.value);
        if (t2 && t2.value) talleristas.push(t2.value);

        if (talleristas.length > 2) {
            errorBox.textContent =
                "Solo puedes seleccionar hasta 2 talleristas.";
            errorBox.classList.remove("d-none");
            return;
        }

        const formData = new FormData(form);
        formData.delete("talleristas[]");
        talleristas.forEach((id) => formData.append("talleristas[]", id));

        fetch("/agenda/store", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: formData,
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    calendar.refetchEvents();
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
});
