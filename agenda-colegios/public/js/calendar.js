document.addEventListener("DOMContentLoaded", function () {
    // 🔹 Elementos del DOM
    const calendarEl = document.getElementById("calendar");
    const modalEl = document.getElementById("eventModal");
    const detailModalEl = document.getElementById("eventDetailModal");
    const form = document.getElementById("eventForm");
    const deleteBtn = document.getElementById("deleteBtn");
    const errorBox = document.getElementById("formErrors");
    const userModalEl = document.getElementById("userModal");
    const userForm = document.getElementById("userForm");

    if (!calendarEl) return;

    // 🔹 Instancias únicas de modales
    const eventModal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const eventDetailModal = detailModalEl
        ? new bootstrap.Modal(detailModalEl)
        : null;
    const userModal = userModalEl ? new bootstrap.Modal(userModalEl) : null;

    // 🔹 Funciones para nuevo/editar usuario
    window.newUser = function () {
        if (!userModal) return;
        userForm.reset();
        document.getElementById("userId").value = "";
        document.getElementById("fieldsFull").style.display = "block";
        document.querySelector("#userModal .modal-title").innerText =
            "Agregar Usuario";
        userModal.show();
    };

    window.editUser = function (u) {
        if (!userModal) return;
        document.getElementById("userId").value = u.id;
        document.getElementById("fieldsFull").style.display = "none";
        document.getElementById("userRol").value = u.rol;
        document.querySelector("#userModal .modal-title").innerText =
            "Editar Rol";
        userModal.show();
    };

    // 🔹 Reset modal usuario al cerrar
    if (userModalEl) {
        userModalEl.addEventListener("hidden.bs.modal", function () {
            userForm.reset();
            document.getElementById("fieldsFull").style.display = "block";
            document.querySelector("#userModal .modal-title").innerText =
                "Agregar Usuario";
        });
    }

    // 🔹 Función para reset modal evento
    function resetModal() {
        if (!form) return;
        form.reset();
        if (errorBox) errorBox.classList.add("d-none");
        if (deleteBtn) deleteBtn.style.display = "none";

        const modalTitle = document.getElementById("modalTitle");
        if (modalTitle) modalTitle.innerText = "Agregar Evento";

        const eventId = document.getElementById("eventId");
        if (eventId) eventId.value = "";

        const tSelectContainer = document.getElementById(
            "talleristaSelectContainer"
        );
        const tResumen = document.getElementById("talleristaResumen");
        const t1Nombre = document.getElementById("t1Nombre");
        const t2Nombre = document.getElementById("t2Nombre");

        if (tSelectContainer) tSelectContainer.classList.remove("d-none");
        if (tResumen) tResumen.classList.add("d-none");
        if (t1Nombre) t1Nombre.innerText = "";
        if (t2Nombre) t2Nombre.innerText = "";
    }

    // 🔹 Inicializar calendario
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        editable: true,
        selectable: true,
        events: "/agenda/events",
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay,listWeek",
        },
        views: {
            multiMonthYear: {
                type: "multiMonth",
                duration: { months: 3 },
                buttonText: "3 Meses",
            },
        },

        // 🔹 Click en fecha
        dateClick: function (info) {
            const dia = info.date.getDay();
            if (dia === 0 || dia === 6) {
                if (
                    !confirm(
                        "Está seleccionando un fin de semana. ¿Desea continuar?"
                    )
                )
                    return;
            }
            resetModal();
            const fecha = document.getElementById("fecha");
            if (fecha) fecha.value = info.dateStr;
            if (eventModal) eventModal.show();
        },

        // 🔹 Click en evento
        eventClick: function (info) {
            resetModal();

            const start = info.event.start;
            const talleristas = info.event.extendedProps.talleristas || [];

            document.getElementById("detailColegio").innerText =
                document.getElementById("colegio_id")?.selectedOptions[0]
                    ?.text ||
                info.event.extendedProps.colegio_id ||
                "No asignado";

            document.getElementById("detailTaller").innerText =
                document.getElementById("taller_id")?.selectedOptions[0]
                    ?.text ||
                info.event.title.split(" - ")[0] ||
                "No asignado";

            document.getElementById("detailFecha").innerText = start
                ? start.toISOString().split("T")[0]
                : "No asignado";
            document.getElementById("detailHora").innerText = start
                ? start.toTimeString().slice(0, 5)
                : "No asignado";
            document.getElementById("detailT1").innerText =
                talleristas[0]?.nombre_completo || "No asignado";
            document.getElementById("detailT2").innerText =
                talleristas[1]?.nombre_completo || "No asignado";

            document.getElementById(
                "detailTalleristasContainer"
            ).style.display = talleristas.length > 0 ? "block" : "none";

            if (eventDetailModal) eventDetailModal.show();

            document.getElementById("openEditModalBtn").onclick = function () {
                if (eventDetailModal) eventDetailModal.hide();
                resetModal();

                document.getElementById("modalTitle").innerText =
                    "Editar Evento";
                document.getElementById("eventId").value = info.event.id;

                const fecha = document.getElementById("fecha");
                const hora = document.getElementById("hora");
                if (fecha) fecha.value = start.toISOString().split("T")[0];
                if (hora) hora.value = start.toTimeString().slice(0, 5);

                const colegioSelect = document.getElementById("colegio_id");
                const tallerSelect = document.getElementById("taller_id");
                if (colegioSelect)
                    colegioSelect.value = info.event.extendedProps.colegio_id;
                if (tallerSelect)
                    tallerSelect.value = info.event.extendedProps.taller_id;

                const userRoleMeta = document.querySelector(
                    'meta[name="user-role"]'
                );
                const userRole = userRoleMeta ? userRoleMeta.content : "";

                if (["admin", "supervisor"].includes(userRole)) {
                    const t1 = document.getElementById("tallerista1");
                    const t2 = document.getElementById("tallerista2");
                    if (t1) t1.value = talleristas[0]?.id || "";
                    if (t2) t2.value = talleristas[1]?.id || "";
                    if (talleristas.length > 0) mostrarResumen();
                }

                if (deleteBtn) deleteBtn.style.display = "inline-block";
                if (eventModal) eventModal.show();
            };
        },

        // 🔹 Arrastrar evento
        eventDrop: function (info) {
            const eventId = info.event.id;
            const newDate = info.event.start.toISOString().split("T")[0];
            const newTime = info.event.start.toTimeString().slice(0, 5);

            const formData = new FormData();
            formData.append("fecha", newDate);
            formData.append("hora", newTime);

            fetch(`/agenda/store?id=${eventId}`, {
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
                    if (!data.success) {
                        alert("No se pudo actualizar la fecha/hora del evento");
                        info.revert();
                    }
                })
                .catch(() => {
                    alert("Error de conexión al actualizar evento");
                    info.revert();
                });
        },
    });

    calendar.render();

    // 🔹 Funciones de talleristas
    function syncTalleristaOptions() {
        const t1 = document.getElementById("tallerista1");
        const t2 = document.getElementById("tallerista2");
        if (!t1 || !t2) return;

        Array.from(t1.options).forEach(
            (o) => (o.disabled = o.value && o.value === t2.value)
        );
        Array.from(t2.options).forEach(
            (o) => (o.disabled = o.value && o.value === t1.value)
        );
    }

    function mostrarResumen() {
        const t1 = document.getElementById("tallerista1");
        const t2 = document.getElementById("tallerista2");
        if (!t1 && !t2) return;

        const tSelectContainer = document.getElementById(
            "talleristaSelectContainer"
        );
        const tResumen = document.getElementById("talleristaResumen");
        const t1Nombre = document.getElementById("t1Nombre");
        const t2Nombre = document.getElementById("t2Nombre");

        if ((t1 && t1.value) || (t2 && t2.value)) {
            tSelectContainer?.classList.add("d-none");
            tResumen?.classList.remove("d-none");
            if (t1Nombre)
                t1Nombre.innerText =
                    t1.selectedOptions[0]?.text || "No asignado";
            if (t2Nombre)
                t2Nombre.innerText =
                    t2.selectedOptions[0]?.text || "No asignado";
        }
    }

    document
        .getElementById("editarTalleristasBtn")
        ?.addEventListener("click", function () {
            document
                .getElementById("talleristaSelectContainer")
                ?.classList.remove("d-none");
            document
                .getElementById("talleristaResumen")
                ?.classList.add("d-none");
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
            if (errorBox) {
                errorBox.textContent =
                    "Solo puedes seleccionar hasta 2 talleristas.";
                errorBox.classList.remove("d-none");
            }
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
                    if (eventModal) eventModal.hide();
                } else if (errorBox) {
                    errorBox.textContent = "Error al guardar el evento.";
                    errorBox.classList.remove("d-none");
                }
            })
            .catch(() => {
                if (errorBox) {
                    errorBox.textContent = "Error de conexión.";
                    errorBox.classList.remove("d-none");
                }
            });
    };
});
