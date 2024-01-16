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
        @foreach($data as $assistance)
            <tr>
                <td></td>
                <td></td>
                <td>{{ $assistance->date }}</td>
                <td>{{ $assistance->nie }}</td>
                <td>{{ $assistance->has_assistance }}</td>
                <td>{{ $assistance->justification_name }}</td>
                <td>{{ $assistance->observacion }}</td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
