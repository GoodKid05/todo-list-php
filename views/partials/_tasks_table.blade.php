@foreach($tableData as $row)
	<tr class="table-task-row" data-task-id="{{ $row['id'] }}">
		<td> <input type="checkbox"> </td>
		@foreach($row['values'] as $value)
			<td>{{ $value ?: 'null' }}</td>
		@endforeach	
	</tr>
@endforeach	
