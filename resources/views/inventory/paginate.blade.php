</tbody>
</table>
</div>
</div>
<div class="card-body py-3 mb-3 d-flex justify-between flex-row-reverse">
    <div class="action-bar">
        {{ $equipments->links('pagination::bootstrap-5') }}
    </div>
    <div class="filter-bar">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <p class="nav-link text-dark">Tất cả <span class="badge bg-info">({{ $totalEquipments }})</span>
                </p>
            </li>
        </ul>
    </div>
</div>
