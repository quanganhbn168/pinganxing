<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use Illuminate\Support\Str;

class PolicyPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = [
            [
                'title' => 'Chính sách bảo mật thông tin',
                'slug' => 'chinh-sach-bao-mat-thong-tin',
                'content' => '<h2>1. Mục đích và phạm vi thu thập thông tin</h2>
<p>Dữ liệu thu thập trên website Ping An Xing chủ yếu bao gồm: Họ tên, email, số điện thoại, nhu cầu dịch vụ và địa chỉ. Ping An Xing sử dụng những thông tin này để:</p>
<ul>
<li>Hỗ trợ khách hàng trong quá trình tìm hiểu, đặt và sử dụng dịch vụ của Ping An Xing.</li>
<li>Giải đáp thắc mắc, tư vấn lịch trình và hỗ trợ thông tin cần thiết.</li>
<li>Gửi thông báo về các tính năng mới, bản cập nhật hoặc các chương trình khuyến mãi.</li>
</ul>

<h2>2. Phạm vi sử dụng thông tin</h2>
<p>Ping An Xing cam kết chỉ sử dụng thông tin cá nhân của khách hàng trong nội bộ công ty. Chúng tôi tuyệt đối <strong>KHÔNG</strong> mua bán, trao đổi hay chia sẻ thông tin khách hàng cho bất kỳ bên thứ ba nào khác vì mục đích thương mại, ngoại trừ các trường hợp có yêu cầu từ cơ quan pháp luật có thẩm quyền.</p>

<h2>3. Thời gian lưu trữ thông tin</h2>
<p>Dữ liệu cá nhân của khách hàng sẽ được lưu trữ bảo mật trên hệ thống của Ping An Xing cho đến khi có yêu cầu hủy bỏ từ chính quý khách hàng qua kênh chăm sóc khách hàng.</p>

<h2>4. Địa chỉ của đơn vị thu thập và quản lý thông tin cá nhân</h2>
<p><strong>PING AN XING</strong></p>
<p>Hệ thống hỗ trợ tiếp nhận qua Email: admin@pinganxing.com hoặc Hotline hiển thị trên website.</p>

<h2>5. Phương tiện và công cụ để người dùng tiếp cận và chỉnh sửa dữ liệu</h2>
<p>Khách hàng có quyền tự kiểm tra, cập nhật, điều chỉnh hoặc hủy bỏ thông tin cá nhân của mình bằng cách liên hệ ban quản trị website Ping An Xing.</p>
',
            ],
            [
                'title' => 'Quy định & Điều khoản dịch vụ',
                'slug' => 'dieu-khoan-dich-vu',
                'content' => '<h2>1. Chấp nhận Điều khoản</h2>
<p>Chào mừng bạn đến với Ping An Xing. Khi bạn truy cập website hoặc đăng ký sử dụng dịch vụ của chúng tôi, bạn mặc nhiên đã đồng ý với các Quy định & Điều khoản dịch vụ này. Vui lòng đọc kỹ trước khi sử dụng.</p>

<h2>2. Cấp phép và Giới hạn sử dụng</h2>
<p>Dịch vụ của Ping An Xing được cung cấp theo phạm vi tư vấn, báo giá và xác nhận với khách hàng. Bạn được hỗ trợ trong thời gian dịch vụ có hiệu lực.</p>
<ul>
<li>Bạn không được quyền sao chép, chỉnh sửa hoặc sử dụng trái phép nội dung, hình ảnh, tài liệu trên hệ thống.</li>
<li>Bạn hoàn toàn chịu trách nhiệm về tính chính xác và hợp pháp của các dữ liệu, giấy tờ, thông tin cá nhân cung cấp cho Ping An Xing.</li>
</ul>

<h2>3. Quyền sở hữu trí tuệ</h2>
<p>Mọi bản quyền thương hiệu, nội dung, hình ảnh và giao diện trên hệ thống đều thuộc quyền sở hữu của Ping An Xing. Việc sử dụng trái phép có thể bị can thiệp bởi pháp luật.</p>

<h2>4. Tạm ngưng và Chấm dứt dịch vụ</h2>
<p>Chúng tôi có quyền tạm ngưng cung cấp dịch vụ nếu phát hiện tài khoản của bạn có dấu hiệu gian lận, vi phạm pháp luật Việt Nam, hoặc bạn chậm trễ trong việc gia hạn thanh toán cước phí theo thỏa thuận hợp đồng.</p>',
            ],
            [
                'title' => 'Chính sách vận chuyển & Giao nhận',
                'slug' => 'chinh-sach-van-chuyen',
                'content' => '<h2>1. Giao nhận Dịch vụ Phần mềm (Sản phẩm số)</h2>
<p>Ping An Xing chuyên cung cấp dịch vụ du lịch, visa, thuê xe và các dịch vụ hỗ trợ liên quan. Với các dịch vụ tư vấn và xử lý hồ sơ, chúng tôi <strong>không vận chuyển sản phẩm vật lý</strong> trừ khi có thỏa thuận riêng.</p>
<ul>
<li><strong>Cách thức giao nhận:</strong> Toàn bộ thông tin tài khoản đăng nhập (Username/Password), tên miền riêng (Sub-domain) và tài liệu hướng dẫn sử dụng sẽ được gửi trực tiếp vào <strong>Địa chỉ Email</strong> hoặc <strong>Zalo</strong> mà quý khách đã cung cấp lúc đăng ký.</li>
<li><strong>Thời gian giao nhận:</strong> Ngay sau khi ký hợp đồng dịch vụ hoặc hệ thống xác nhận thanh toán thành công (thời gian cấu hình tối đa 01 - 04 giờ làm việc).</li>
</ul>

<h2>2. Giao nhận Thiết bị Phần cứng (Nếu có)</h2>
<p>Trong trường hợp quý khách mua thêm các thiết bị phần cứng hỗ trợ bán hàng (Máy in hóa đơn, máy quét mã vạch, ngăn kéo đựng tiền...):</p>
<ul>
<li>Chúng tôi áp dụng hình thức vận chuyển thông qua các đơn vị chuyển phát uy tín (Viettel Post, VNPost, GHTK...) hoặc nhân viên kỹ thuật giao và lắp đặt trực tiếp.</li>
<li><strong>Chi phí và thời gian:</strong> Tùy thuộc vào vị trí địa lý của khách hàng, phí vận chuyển có thể được miễn phí hoặc tính phí thỏa thuận. Thời gian nhận hàng vật lý từ 1-5 ngày làm việc.</li>
</ul>',
            ],
            [
                'title' => 'Chính sách đổi trả & Hoàn tiền',
                'slug' => 'chinh-sach-doi-tra',
                'content' => '<h2>1. Chính sách trải nghiệm (Dùng thử)</h2>
<p>Để đảm bảo sự an tâm, Ping An Xing luôn tư vấn rõ phạm vi dịch vụ, chi phí và các điều kiện liên quan trước khi khách hàng xác nhận sử dụng dịch vụ.</p>

<h2>2. Điều kiện áp dụng hoàn tiền</h2>
<p>Với bản chất là dịch vụ phần mềm lưu trữ trên Cloud đã được dùng thử trước khi mua, chúng tôi <strong>KHÔNG</strong> áp dụng chính sách đổi trả/hoàn tiền đối với các khoản phí thuê bao phần mềm đã được thanh toán, ngoại trừ các trường hợp bất khả kháng sau:</p>
<ul>
<li>Dịch vụ không thể thực hiện do lỗi phát sinh từ phía Ping An Xing và hai bên không thể thống nhất phương án thay thế phù hợp.</li>
<li>Thanh toán bị trùng lặp do lỗi hệ thống cổng thanh toán. Trong trường hợp này, khoản tiền dư sẽ được hoàn lại 100% trong vòng 3-5 ngày làm việc.</li>
</ul>

<h2>3. Đối với Thiết bị phần cứng</h2>
<p>Nếu sản phẩm vật lý hoặc voucher do Ping An Xing cung cấp có lỗi phát sinh từ phía nhà cung cấp, chúng tôi sẽ hỗ trợ xử lý theo chính sách đã thỏa thuận với khách hàng.</p>',
            ],
            [
                'title' => 'Chính sách thanh toán',
                'slug' => 'chinh-sach-thanh-toan',
                'content' => '<h2>1. Các hình thức thanh toán</h2>
<p>Để mang đến sự tiện lợi khi đặt dịch vụ, Ping An Xing cung cấp các hình thức thanh toán sau:</p>
<ul>
<li><strong>Chuyển khoản ngân hàng:</strong> Đây là phương thức phổ biến nhất. Khách hàng có thể chuyển khoản số tiền tương ứng gói cước tới số tài khoản công ty được ghi chú trên Hợp đồng hoặc Hóa đơn điện tử.</li>
<li><strong>Thanh toán trực tuyến:</strong> Thanh toán qua QR Code, ví điện tử hoặc cổng thanh toán được Ping An Xing xác nhận.</li>
<li><strong>Thanh toán Tiền mặt:</strong> Áp dụng khi có nhân viên Ping An Xing xác nhận và thu trực tiếp theo thỏa thuận.</li>
</ul>

<h2>2. Quy định thanh toán & Gia hạn</h2>
<ul>
<li>Cước phí dịch vụ phần mềm là phí trả trước (Pre-paid) theo chu kỳ (1/3/6 tháng hoặc 1 năm). Quý khách vui lòng thanh toán để duy trì hoặc khởi tạo dịch vụ.</li>
<li>Hệ thống sẽ tự động gửi thông báo nhắc nhở gia hạn qua Email/Phần mềm trước 7-15 ngày khi gói cước sắp hết hạn.</li>
<li>Ping An Xing bảo lưu quyền tạm ngưng xử lý dịch vụ nếu khách hàng chậm thanh toán quá thời hạn đã thỏa thuận.</li>
</ul>'
            ]
        ];

        foreach ($policies as $policyData) {
            $slug = $policyData['slug'];
            unset($policyData['slug']);

            // Insert into pages table based on title -> update content
            $page = Page::updateOrCreate(
                ['title' => $policyData['title']],
                [
                    'content' => $policyData['content'],
                    'status' => true,
                ]
            );

            // Manual slug injection compatible with HasSlug logic
            $page->slugData()->updateOrCreate(
                [],
                ['slug' => $slug]
            );
        }
    }
}
