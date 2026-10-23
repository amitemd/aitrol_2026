<tr class="bg-white border-b hover:bg-gray-50 ">
    <th scope="row" colspan="2" class="px-6 py-4 font-medium text-gray-900 whitespace-normal max-w-[550px] ">
        {{$item["pregunta"]}}
    </th>
    <td colspan="5" class="px-6 py-4">
        <textarea id="message" name="{{$item['id']}}" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Escriba su comentario"></textarea>
    </td>
</tr>