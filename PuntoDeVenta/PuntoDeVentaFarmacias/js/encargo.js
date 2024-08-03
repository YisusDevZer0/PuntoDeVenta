document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('medicamentos_container');
    const addButton = document.getElementById('add_medicamento');
    let count = 1;

    addButton.addEventListener('click', () => {
        const div = document.createElement('div');
        div.className = 'medicamento';
        div.innerHTML = `
            <div class="mb-3">
                <label for="medicamento" class="form-label">Medicamento</label>
                <select class="form-control" name="medicamentos[${count}][id]" required>
                    <?php
                    include_once "Controladores/ControladorUsuario.php";
                    $query = "SELECT ID_Prod_POS, Nombre_Prod FROM Productos_POS";
                    if ($result = $mysqli->query($query)) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value=\"{$row['ID_Prod_POS']}\">{$row['Nombre_Prod']}</option>";
                        }
                        $result->free();
                    }
                    $mysqli->close();
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad</label>
                <input type="number" class="form-control" name="medicamentos[${count}][cantidad]" required>
            </div>
        `;
        container.appendChild(div);
        count++;
    });
});
