@if($status === 'active')
    <span class="px-2 py-1 bg-blue-600 text-white rounded-lg">Active</span>
@elseif($status === 'upcoming')
    <span class="px-2 py-1 bg-yellow-500 text-white rounded-lg">Upcoming</span>
@elseif($status === 'passed')
    <span class="px-2 py-1 bg-green-500 text-white rounded-lg">Completed</span>
@else
    <span class="px-2 py-1 bg-red-500 text-white rounded-lg">Delay</span>
@endif
