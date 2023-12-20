<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>ID Sección</th>
            <th>Fecha</th>
            <th>NIE</th>
            <th>Faltó</th>
            <th>Justificación</th>
            <th>Observación</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
            <tr>
                <td></td>
                <td></td>
                <td>{{ $item->date }}</td>
                <td>{{ $item->nie }}</td>
                <td>{{ $item->falto }}</td>
                <td>{{ $item->justificacion }}</td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
