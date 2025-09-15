document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");
    const modalEl = document.getElementById("eventModal");
    const form = document.getElementById("eventForm");
    const deleteBtn = document.getElementById("deleteBtn");
    const errorBox = document.getElementById("formErrors");

    // 🔹 Reset modal
    function resetModal() {
        const modalTitle = document.getElementById("modalTitle");
        if (modalTitle) modalTitle.innerText = "Agregar Evento";

        form.reset();
        const eventId = document.getElementById("eventId");
        if (eventId) eventId.value = "";

        errorBox?.classList.add("d-none");
        if (deleteBtn) deleteBtn.style.display = "none";

        const tSelectContainer = document.getElementById(
            "talleristaSelectContainer"
        );
        const tResumen = document.getElementById("talleristaResumen");
        const t1Nombre = document.getElementById("t1Nombre");
        const t2Nombre = document.getElementById("t2Nombre");

        tSelectContainer?.classList.remove("d-none");
        tResumen?.classList.add("d-none");
        if (t1Nombre) t1Nombre.innerText = "";
        if (t2Nombre) t2Nombre.innerText = "";
    }

    // 🔹 Calendar init
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth", // vista inicial
        editable: true,
        selectable: true,
        events: "/agenda/events",

        // 🔹 Toolbar superior con todas las vistas
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay,listWeek",
        },

        views: {
            multiMonthYear: {
                type: "multiMonth",
                duration: { months: 3 }, // por ejemplo, 3 meses
                buttonText: "3 Meses",
            },
        },

        dateClick: function (info) {
            resetModal();
            const fecha = document.getElementById("fecha");
            if (fecha) fecha.value = info.dateStr;

            new bootstrap.Modal(modalEl).show();
        },

        eventClick: function (info) {
            resetModal();
            // 🔹 Llenar modal de detalle
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

            // 🔹 Mostrar modal de detalle
            const detailModal = new bootstrap.Modal(
                document.getElementById("eventDetailModal")
            );
            detailModal.show();

            // 🔹 Botón "Editar" abre modal de edición
            document.getElementById("openEditModalBtn").onclick = function () {
                detailModal.hide(); // cerrar modal de detalle
                resetModal(); // limpiar modal de edición
                // Llenar modal de edición
                document.getElementById("modalTitle").innerText =
                    "Editar Evento";
                document.getElementById("eventId").value = info.event.id;
                const fecha = document.getElementById("fecha");
                const hora = document.getElementById("hora");
                if (fecha) fecha.value = start.toISOString().split("T")[0];
                if (hora) hora.value = start.toTimeString().slice(0, 5);

                // Colegio y taller
                const colegioSelect = document.getElementById("colegio_id");
                const tallerSelect = document.getElementById("taller_id");
                if (colegioSelect)
                    colegioSelect.value = info.event.extendedProps.colegio_id;
                if (tallerSelect)
                    tallerSelect.value = info.event.extendedProps.taller_id;

                // Talleristas
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

                new bootstrap.Modal(modalEl).show();
            };
        },
        eventDrop: function (info) {
            const eventId = info.event.id;
            const newDate = info.event.start.toISOString().split("T")[0];
            const newTime = info.event.start.toTimeString().slice(0, 5);

            // Preparar FormData
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
                        info.revert(); // Revertir si hubo error
                    }
                })
                .catch(() => {
                    alert("Error de conexión al actualizar evento");
                    info.revert();
                });
        },
    });

    calendar.render();

    // 🔹 Evita duplicados en selects
    function syncTalleristaOptions() {
        const t1 = document.getElementById("tallerista1");
        const t2 = document.getElementById("tallerista2");
        if (!t1 || !t2) return;

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
        if (!t1 && !t2) return;

        if ((t1 && t1.value) || (t2 && t2.value)) {
            const tSelectContainer = document.getElementById(
                "talleristaSelectContainer"
            );
            const tResumen = document.getElementById("talleristaResumen");
            const t1Nombre = document.getElementById("t1Nombre");
            const t2Nombre = document.getElementById("t2Nombre");

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
            const tSelectContainer = document.getElementById(
                "talleristaSelectContainer"
            );
            const tResumen = document.getElementById("talleristaResumen");

            tSelectContainer?.classList.remove("d-none");
            tResumen?.classList.add("d-none");
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
