<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: linear-gradient(135deg, #f78ca0 0%, #f9748f 19%, #fd868c 60%, #fe9a8b 100%); min-height: 100vh; padding: 30px 20px; color: #333; }
        .container { max-width: 1100px; margin: 0 auto; }
        .back { display: inline-block; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 500; margin-bottom: 16px; }
        .back:hover { opacity: 0.8; text-decoration: underline; }
        .card-header { background: #ffffff; border-radius: 16px; padding: 24px 30px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
        .card-header h2 { font-size: 22px; font-weight: 800; color: #222; text-transform: uppercase; }
        .card-header p { font-size: 13px; color: #777; margin-top: 4px; }
        .search-box { display: flex; gap: 10px; align-items: center; flex: 1; max-width: 380px; }
        .search-input { width: 100%; padding: 10px 16px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; }
        .search-input:focus { border-color: #e91e63; }
        .btn-add-new { background-color: #e91e63; color: white; text-decoration: none; padding: 10px 18px; border-radius: 10px; font-weight: 600; font-size: 14px; box-shadow: 0 4px 12px rgba(233, 30, 99, 0.3); display: inline-block; }
        .btn-add-new:hover { background-color: #d81b60; }
        .table-card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background-color: #f78ca0; color: white; }
        th { padding: 16px; font-size: 13px; font-weight: 700; text-transform: uppercase; }
        td { padding: 16px; border-bottom: 1px solid #f0f0f0; font-size: 14px; vertical-align: middle; }
        tbody tr:hover { background-color: #fff8f9; }
        .table-badge { background-color: #fce4ec; color: #d81b60; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 13px; display: inline-block; }
        .status-badge { font-weight: 600; font-size: 13px; }
        .status-pending { color: #f57c00; }
        .status-completed { color: #388e3c; }
        .action-btns { display: flex; gap: 6px; align-items: center; }
        .btn { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; border: none; cursor: pointer; display: inline-block; }
        .btn-add-more { background-color: #2196f3; color: white; }
        .btn-edit { background-color: #ff9800; color: white; }
        .btn-delete { background-color: #f44336; color: white; }
        .btn-detail { background-color: #3f51b5; color: white; }
    </style>
</head>
<body>

<div class="container">
    <a href="<?= BASE_URL ?>/" class="back">← Quay về Trang chủ</a>

    <div class="card-header">
        <div>
            <h2>QUẢN LÝ ĐƠN HÀNG</h2>
            <p>Danh sách đơn hàng tại nhà hàng</p>
        </div>

        <div class="search-box">
            <input type="text" id="searchInput" class="search-input" onkeyup="filterOrders()" placeholder="🔍 Tìm theo số bàn hoặc tên...">
        </div>

        <!-- HIỂN THỊ NÚT ĐẶT MÓN CHO TẤT CẢ TÀI KHOẢN -->
        <a href="<?= BASE_URL ?>/orders/create" class="btn-add-new">+ Đặt món mới</a>
    </div>

    <div class="table-card">
        <table id="ordersTable">
            <thead>
                <tr>
                    <th>TÊN KHÁCH</th>
                    <th>SỐ BÀN</th>
                    <th>NGÀY ĐẶT</th>
                    <th>TỔNG TIỀN</th>
                    <th>TRẠNG THÁI</th>
                    <th>HÀNH ĐỘNG</th>
                    <th>CHI TIẾT</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <?php 
                            $name   = is_object($order) ? $order->customer_name : $order['customer_name'];
                            $table  = is_object($order) ? $order->table_number : $order['table_number'];
                            $date   = is_object($order) ? $order->order_date : $order['order_date'];
                            $total  = is_object($order) ? $order->total_amount : $order['total_amount'];
                            $status = is_object($order) ? $order->status : $order['status'];
                            $id     = is_object($order) ? $order->id : $order['id'];
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($name) ?></strong></td>
                            <td><span class="table-badge"><?= htmlspecialchars($table) ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($date)) ?></td>
                            <td style="color: #e91e63; font-weight: 700;"><?= number_format($total) ?>đ</td>
                            <td>
                                <span class="status-badge <?= $status === 'Đang xử lý' ? 'status-pending' : 'status-completed' ?>">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <?php if ($status === 'Đang xử lý'): ?>
                                        <a href="<?= BASE_URL ?>/orders/create?table=<?= urlencode($table) ?>&name=<?= urlencode($name) ?>" class="btn btn-add-more">
                                           + Thêm món
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($isAdmin)): ?>
                                        <a href="<?= BASE_URL ?>/orders/edit?id=<?= $id ?>" class="btn btn-edit">Sửa</a>
                                        <a href="<?= BASE_URL ?>/orders/delete?id=<?= $id ?>" class="btn btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn này?');">Xóa</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/orders/detail?id=<?= $id ?>" class="btn btn-detail">Xem chi tiết</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #888; padding: 40px 20px;">
                            <p style="margin-bottom: 12px;">Chưa có đơn hàng nào.</p>
                            <a href="<?= BASE_URL ?>/orders/create" class="btn-add-new">
                                🍽️ Bấm vào đây để đặt món ngay
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filterOrders() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#ordersTable tbody tr');

    rows.forEach(row => {
        const name = row.cells[0] ? row.cells[0].textContent.toLowerCase() : '';
        const table = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';

        if (name.includes(input) || table.includes(input)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

</body>
</html>