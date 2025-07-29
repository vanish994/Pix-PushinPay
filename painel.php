<?php
$db = new SQLite3(__DIR__ . '/transacoes.db');

$result = $db->query("SELECT * FROM transacoes ORDER BY data_hora DESC");

echo "<h2>Histórico de Transações PIX</h2>";
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>ID</th><th>Status</th><th>Valor (R$)</th><th>Data/Hora</th></tr>";

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $valor = number_format($row['valor'] / 100, 2, ',', '.');
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['status']}</td>";
    echo "<td>R$ {$valor}</td>";
    echo "<td>{$row['data_hora']}</td>";
    echo "</tr>";
}

echo "</table>";
