-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2025 at 02:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ql_cosothuctap`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`, `role`) VALUES
(1, 'admin123@gmail.com', '123', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `bao_cao_thuc_tap`
--

CREATE TABLE `bao_cao_thuc_tap` (
  `stt_baocao` int(11) NOT NULL,
  `ma_dang_ky` int(11) NOT NULL,
  `ma_tuyen_dung` varchar(50) NOT NULL,
  `noi_dung` text NOT NULL,
  `ngay_gui` date DEFAULT curdate(),
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bao_cao_thuc_tap`
--

INSERT INTO `bao_cao_thuc_tap` (`stt_baocao`, `ma_dang_ky`, `ma_tuyen_dung`, `noi_dung`, `ngay_gui`, `file_path`) VALUES
(15, 14, 'TD17476276377537', 'Báo cáo', '2025-05-20', '../Uploads/baocao_1747680229_Báo cáo 1.pdf'),
(19, 19, 'TD17478449812802', 'báo cáo', '2025-06-03', '../Uploads/baocao_1748902566_Báo cáo 2.pdf'),
(20, 20, 'TD17476301692948', 'Báo tuần này', '2025-06-03', '../Uploads/baocao_1748929879_Báo cáo 1.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `cong_ty`
--

CREATE TABLE `cong_ty` (
  `stt_cty` int(11) NOT NULL,
  `ten_cong_ty` varchar(255) NOT NULL,
  `dia_chi` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gioi_thieu` text DEFAULT NULL,
  `trang_thai` enum('Đang chờ','Đã duyệt','Bị từ chối') DEFAULT 'Đang chờ',
  `logo` varchar(255) DEFAULT NULL,
  `anh_bia` varchar(255) DEFAULT NULL,
  `quy_mo` varchar(255) DEFAULT NULL,
  `linh_vuc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cong_ty`
--

INSERT INTO `cong_ty` (`stt_cty`, `ten_cong_ty`, `dia_chi`, `so_dien_thoai`, `email`, `gioi_thieu`, `trang_thai`, `logo`, `anh_bia`, `quy_mo`, `linh_vuc`) VALUES
(13, 'Công ty Cổ phần Công nghệ Tài chính Goline', 'Tòa Nhà Kim Ánh, 78 Duy Tân, Cầu Giấy, Hà Nội', '0987654345', 'Goline@gmail.com', 'Goline Corporation là công ty có vốn đầu tư Nhật Bản, chuyên phát triển hệ thống phần mềm chứng khoán, tài chính cho VN, Nhật Bản, Hong Kong. Goline luôn hướng tới việc cung cấp những sản sản phẩm tinh tế bằng những giải pháp, công nghệ tiên tiến nhất như slogan “Smart but Simple”. Để đạt được điều đó, chúng tôi luôn cố gắng không ngừng, liên tục học hỏi, liên tục có những chương trình đào tạo cho tất cả cán bộ công ty để đi nhanh hơn so với thị trường. Ngoài chuyên môn, việc học những kĩ năng như nâng cao khả năng tiếp cận, kĩ năng quản lý công việc, kĩ năng học tập tối ưu, phương pháp giải tỏa stress, thiền cho kĩ sư,…đã mang lại những giá trị không chỉ trong công việc mà còn cải thiện rất nhiều trong cuộc sống.\r\n\r\n· Quy trình quản lý chất lượng ISO 9001:2015\r\n\r\n· Quy trình quản lý an toàn thông tin ISO 27001:2015\r\n\r\nCác giải thưởng tiêu biểu :\r\n\r\n- Sao khuê 5 sao do Vinasa tổ chức\r\n\r\n- Top 10 Chuyển đối số do Bộ TTTT tổ chức\r\n\r\n - Giải Bạc sản phẩm sáng tạo Make in Việt Nam do bộ TTTT tổ chức', 'Đã duyệt', '1747626612_logo_Công ty Cổ phần Công nghệ Tài chính Goline.webp', '1747626612_anhbia_golinebia.webp', '200', 'công nghệ thông tin'),
(14, 'SOLIS LAB', 'Tầng 4 tòa nhà Hà Thành Plaza 102 Thái Thịnh, Đống Đa, Hanoi, Vietnam', '0987656588', 'info@solislab.com', '3 Lý do để gia nhập công ty\r\nSmall cohesive team of excellent individuals\r\nSkill up quickly at web dev, UX and design\r\nBonuses for project completion and top performers', 'Đã duyệt', '1747627525_logo_SOLIS LAB.webp', '1747627525_anhbia_SOLIS LAB bia.jpg', '60', 'công nghệ thông tin'),
(15, 'MEGAZONE', '54 Lieu Giai, Quận Ba Đình, Hà Nội', '0987876546', 'MEGAZONE@gmail.com', 'Người tiên phong trong lĩnh vực đám mây\r\n\r\nChúng tôi là một công ty về công nghệ đám mây. Megazone đã và đang mang đến những trải nghiệm kinh doanh vượt trội cùng với kiến thức chuyên môn sâu rộng trong các lĩnh vực Cloud & Hosting, Tiếp thị kỹ thuật số và Đại lý kỹ thuật số cho các khách hàng quý giá của mình từ năm 1998. Là đối tác chính thức đầu tiên của AWS tại Hàn Quốc, Megazone là công ty dẫn đầu thị trường đám mây tại Hàn Quốc, chuyên về kinh doanh điện toán đám mây. Chúng tôi đã được trao giải Đối tác của năm 2017 khu vực Châu Á - Thái Bình Dương (APAC). Megazone hiện có mặt tại Hàn Quốc, Việt Nam, Hoa Kỳ và Nhật Bản.', 'Đã duyệt', 'logo_15_1747629462.png', '1747628524_anhbia_bìa.jpg', '70', 'công nghệ thông tin'),
(16, 'CÔNG TY CỔ PHẦN TẬP ĐOÀN THIÊN KHÔI', 'Tầng 2 tòa nhà MIPEC Tower, 229 Tây Sơn, Đống Đa, Hà Nội', '0956734523', 'thienkhoi@gmail.com', 'Tập đoàn Bất Động Sản Thiên Khôi\r\n\r\nHội sở chính: Tòa nhà Mipec, 229 Tây Sơn, Đống Đa, Hà Nội\r\n\r\nNgày 01 tháng 01 năm 2020, Tập đoàn Bất động sản Thiên Khôi chính thức có mặt trên thị truờng Bất động sản Việt Nam với sứ mệnh kết nối khách hàng và chủ nhà, tư vấn về pháp lý và tình trạng bất động sản trước khi giao dịch diễn ra, điều tiết làm tăng tính thanh khoản cho hàng ngàn Bất động sản trên khắp các Quận nội và ngoại thành tại Hà Nội và TP HCM, làm trong sạch và minh bạch thị trường.\r\n\r\nTrải qua hơn 3 năm hình thành và phát triển Công ty đã có hơn 30.000 nhân sự, 20 Trụ sở từ Bắc vào Nam với hàng ngàn giao dịch Bất động sản lớn nhỏ trên thị trường Hà Nội và TP. Hồ Chí Minh.\r\n\r\nThiên Khôi hoạt động theo mô hình Tập đoàn cổ phần, bao gồm các khối/phòng chức năng, các đội nhóm bán hàng theo khu vực tập trung tạo tính gắn kết và hiệu quả tốt.\r\n\r\nMỗi cá nhân tại Bất động sản Thiên Khôi đều mang trong mình một trách nhiệm lớn lao đó là kết nối, sẻ chia, xây đắp để tạo nên những giá trị nhằm hướng tới sự phát triển chung của cộng đồng xã hội. Đến với Công ty Bất động sản Thiên Khôi bằng tính chuyên nghiệp, sự đào tạo bài bản, cùng với đó là sự nỗ lực không ngừng chắc chắn sẽ tiến mạnh, thu nhập giàu sang.', 'Đã duyệt', '1747628906_logo_CÔNG TY CỔ PHẦN TẬP ĐOÀN THIÊN KHÔI.webp', '1747628906_anhbia_bìa.jpg', '1000', 'Kinh doanh'),
(17, 'CÔNG TY TNHH Á CHÂU LOGISTICS', '45 Trần Thái Tông, Quận Cầu Giấy, Thành phố Hà Nội', '0987654322', 'achau@gmail.com', 'Á Châu logistics Là đơn vị cung cấp dịch vụ trung gian nhập hàng từ các website thương mại điện tử hàng đầu Trung Quốc. Dịch vụ của chúng tôi bao gồm:\r\n\r\n- Tư vấn tìm kiếm nguồn hàng trên các website bán buôn, bán lẻ hàng đầu Trung Quốc như alibaba.com, 1688.com, taobao.com, tmall.com...\r\n\r\n- Mua hàng và kiểm tra hàng hóa\r\n\r\n- Đóng gói và làm việc với các đơn vị vận chuyển hàng hóa về Việt Nam\r\n\r\n- Khiếu nại nhà cung cấp, hỗ trợ đổi trả hàng hóa\r\n\r\nMục tiêu của Á Châu logistics là luôn dẫn đầu trong việc xây dựng tiêu chuẩn dịch vụ để phục vụ khách hàng ngày một tốt hơn. Á Châu logistics luôn tự hào là đơn vị tiên phong thực hiện những cam kết mang tính thay đổi, dẫn dắt và đòi hỏi cao đối với thị trường:\r\n\r\n1. Chúng tôi thực hiện việc đặt hàng bằng công cụ và quản lý đơn hàng hoàn toàn trực tuyến, không thông qua file excel. Việc mua hàng thực hiện hàng ngày, không chờ thời gian chốt đơn.\r\n\r\n2. Chúng tôi đào tạo nhân lực kiểm hàng ngay tại Trung Quốc để giảm thiểu rủi ro và chi phí tài chính cho khách hàng nhập hàng.\r\n\r\n3. Chúng tôi thực hiện hệ thống kiểm soát mọi trạng thái đơn hàng và giao tiếp trực tuyến. Tới thời điểm này rất nhiều dịch vụ nhập hàng chưa thể đáp ứng được.\r\n\r\n4. Chúng tôi nâng cao tiêu chuẩn hỗ trợ khách hàng 24 / 7.\r\n\r\n5. Chúng tôi triển khai dịch vụ Chuyển phát nhanh với những cam kết nghiêm ngặt về thời gian vận chuyển.\r\n\r\n6. Chúng tôi cam kết không thu phí mua hàng nếu không thực hiện mua hàng trong vòng 24h.\r\n\r\n7. Và còn rất nhiều những tiêu chuẩn dịch vụ mà Bạn chỉ cảm nhận được khi sử dụng dịch vụ của chúng tôi\r\n\r\nChúng tôi hiểu rằng, để tiếp tục tồn tại trên thị trường, Á Châu logistics cần liên tục nâng cao chất lượng dịch vụ. Vì vậy, Á Châu logistics luôn kỳ vọng được hợp tác với những Khách hàng có những yêu cầu khắt khe nhất. Đó là tôn chỉ trong mọi hành động của Á Châu logistics trong suốt thời gian qua.', 'Đã duyệt', '1747629697_logo_CÔNG TY TNHH Á CHÂU LOGISTICS.webp', '1747629697_anhbia_bìa.jpg', '100-499', 'kinh tế'),
(18, 'CÔNG TY LUẬT TNHH ĐẦU TƯ QUỐC TẾ AN PHÁT', 'Tầng 2, 3 nhà số 38 ngách 124/35 Miêu Nha, TDP 1, Phường Tây Mỗ, Quận Nam Từ Liêm, Hà Nội', '0989856437', 'anphat@gmail.com', 'Luật đầu tư quốc tế An Phát – 安发国际投资法律\r\n\r\nTư vấn đầu tư nước ngoài – 外国投资咨询\r\nTư vấn luật doanh nghiệp – 企业法律咨询\r\nTư vấn hợp đồng – 企业法律咨询\r\nGiải quyết tranh cấp kinh doanh thương mại – 企业法律咨询\r\nTư vấn pháp luật hình sự quốc tế – 国际刑法咨询', 'Đã duyệt', '1747629968_logo_CÔNG TY LUẬT TNHH ĐẦU TƯ QUỐC TẾ AN PHÁT.webp', '1747629968_anhbia_bìa.jpg', '25-99', 'Luật'),
(19, 'CÔNG TY CỔ PHẦN ĐẦU TƯ THƯƠNG MẠI DỊCH VỤ QUỐC TẾ PHÚC AN', '21 Ngọc Hà, Phường Ngọc Hà, Quận Ba Đình, Hà Nội', '0675348893', 'phucan@gmail.com', 'Nghành nghề kinh doanh: làm visa, giấy phép lao động, thẻ tạm trú, book vé máy bay, tour du lịch', 'Đã duyệt', '1747630362_logo_CÔNG TY CỔ PHẦN ĐẦU TƯ THƯƠNG MẠI DỊCH VỤ QUỐC TẾ PHÚC AN.webp', '1747630362_anhbia_bìa.jpg', '10-24', 'Luật'),
(20, 'Công ty cổ phần tập đoàn Minh Anh', 'Tầng 11, tòa nhà Veam Tây Hồ, ngõ 689 đường Lạc Long Quân, Phường Phú Thượng, Quận Tây Hồ, Hà Nội', '0987657642', 'minhanh@gmail.com', 'LỊCH SỬ PHÁT TRIỂN\r\n\r\nTỪ 1990 - 2000: KHỞI NGHIỆP XỨ NGƯỜI\r\n\r\nTập Đoàn Minh Anh (Mian Group) khởi nghiệp tại Châu Âu bởi một nhóm cổ đông Việt Kiều sinh sống tại CHLB Đức từ những năm 1990. với thương hiệu thời trang riêng , tên tuổi của chúng tôi được biết đến tại khắp 10 nước Châu Âu như Đức, Anh,Pháp,Séc,BaLan,Hungary và một số nước khác\r\n\r\nTỪ 2000 - 2010: KHÁT VỌNG HỒI HƯƠNG\r\n\r\nĐầu những năm 2000, nhóm cổ đông chuyển hướng đầu tư về Việt Nam bằng việc xây dựng các nhà máy may và chuỗi cung ứng hàng xuất khẩu may mặc cho thị trường Châu Âu và Mỹ với hơn 10.000 nhân viên và công nhân may tại Việt Nam. Khi nhận thấy tiềm năng và cơ hội phát triển ngành công nghiệp không khói ở Việt Nam, MIAN GROUP đã định hướng phát triển ĩnh vực này thành mảng mũi nhọn và chủ trương xây dượng một chuỗi các Khách Sạn và khu nghỉ dưỡng cao cấp hàng đầu Việt nam mang tên Amiana tại Nha Trang, Cam Ranh,Mũi Né, Phú Quốc và các thành phố lớn\r\n\r\n2010 ĐẾN NAY: 2000-2010: VỮNG BƯỚC VƯƠN XA\r\n\r\nHiện nay,ngoài các lĩnh vực đang hoạt động, MIAN GROUP đã mở rộng sang lĩnh vực du lịch sinh thái và phát triển thành Tập Đoàn đa ngành nghề với phạm vi kinh doanh cả nước\r\n\r\nTên Giao Dịch: Mian Group\r\n\r\nMST: 0103947554\r\n\r\n Mian Group là tổng công ty của các công ty như công ty Sơn Hà Nội, công ty may Mian Apparel, chuỗi nghỉ dưỡng Amiana , và các khối BĐS....\r\n\r\n  Địa chỉ: Tầng 11, tòa nhà Veam Tây Hồ, ngõ 689 đường Lạc Long Quân, Phường Phú Thượng, Quận Tây Hồ, Hà Nội', 'Đã duyệt', '1747630761_logo_Công ty cổ phần tập đoàn Minh Anh.webp', '1747630761_anhbia_bìa.jpg', '100', 'ngôn ngữ'),
(21, 'CÔNG TY TNHH SÁNG TẠO VÀ KẾT NỐI SIGMA (SCCON)', 'Tầng 2, tòa G1, Vinhomes Greenbay, Mễ Trì, Hà Nội', '0876577898', 'sigma@gmail.com', 'Chúng tôi là hệ thống 4T - Hệ sinh thái chăm sóc sức khỏe. Hệ thống của chúng tôi có nhiều sản phẩm và các hoạt động từ:  Sản xuất dược, đông dược; Thực phẩm chức năng; Hạt dinh dưỡng, ...\r\n\r\nCác công ty thành viên của chúng tôi có:\r\n\r\nCông ty TNHH Kết nối và Sáng tạo Sigma (SCCON holding).\r\nCông ty TNHH Công nghệ Dược phẩm Lotus.\r\nCông ty cổ phần chăm sóc sức khỏe tự nhiên 3H.\r\nVà các công ty thành viên khác trong hệ thống 4T.\r\n\r\n\r\nGIÁ TRỊ XUYÊN SUỐT TRONG HOẠT ĐỘNG CỦA HỆ THỐNG: Được thể hiện xuyên suốt trên website: http://lotuspharma.net.vn/\r\n\r\nSỨ MỆNH:\r\n\r\nGóp phần bảo tồn, phát triển dược liệu Việt Nam và các bài thuốc Nam chân truyền.\r\nHoàn thiện và phát triển đội ngũ không giới hạn.\r\n\r\nTẦM NHÌN đến năm 2028, Lotus mong muốn:\r\n\r\nTrở thành hệ thống hàng đầu khu vực về thuốc Nam và dược liệu Nam.\r\nCó đội ngũ chuyên nghiệp hàng đầu trong ngành Y dược.\r\n\r\nGIÁ TRỊ CỐT LÕI\r\n\r\nHọc, thực hành và hoàn thiện mình mạnh mẽ về Thái độ, Kỹ năng, Kiến thức.\r\nTận tâm trong mọi việc.\r\nĐặt khách hàng là trung tâm.\r\nChân thành trong hợp tác.\r\nLấy chất lượng làm nền tảng.\r\nTRIẾT LÝ KINH DOANH: Lotus đầu tư xây dựng và đào tạo đội ngũ chuyên nghiệp, để đáp ứng các yêu cầu ngày càng cao của khách hàng về tiến độ, chất lượng và chi phí.\r\n\r\nVĂN HÓA CÔNG TY\r\n\r\nChuyên nghiệp.\r\nĐoàn kết.\r\nChia sẻ.\r\n\r\nPHƯƠNG PHÁP QUẢN TRỊ\r\n1. Quản trị theo mục tiêu\r\n\r\nXây dựng mục tiêu SMART\r\nXây dựng biện pháp.\r\nXây dựng kế hoạch và bộ KPI\r\nThực hiện kế hoạch và đo KPI\r\nCải tiến\r\n2. Quản trị chất lượng hoạt động theo 5 bước.\r\n\r\nXây dựng tiêu chuẩn.\r\nXây dựng quy trình.\r\nĐào tạo.\r\nThực hiện.\r\nCải tiến.\r\n\r\nQUẢN LÝ CHẤT LƯỢNG: Sản xuất ra những sản phẩm chất lượng cao và ổn định là nguyên tắc cốt lõi trong hoạt động của nhà máy. Lotus cam kết kiểm soát chặt chẽ để loại bỏ hoàn toàn sản phẩm lỗi trên từng công đoạn.', 'Đã duyệt', '1747631043_logo_CÔNG TY TNHH SÁNG TẠO VÀ KẾT NỐI SIGMA (SCCON).webp', '1747631043_anhbia_bìa.jpg', '25-99', 'Dược'),
(22, 'CÔNG TY TNHH DU LỊCH VICTORIA', 'Tầng 12, số 29 Phạm Văn Bạch, Yên Hòa, Cầu Giấy, Hà Nội, Việt Nam', '0987655676', 'VICTORIA@gmail.com', 'Công ty TNHH Du lịch Victoria (còn gọi là Victoriatour) - thành lập vào năm 2012, là một công ty chuyên cung cấp và mang những nền văn hóa đa dạng ở Việt Nam đến hàng ngàn khách du lịch từ Italia, Singapore, Nga, Trung quốc, Hàn Quốc, v.v. Với mục tiêu đưa du lịch Việt Nam đến gần với Thế giới Victoriatour đã ra đời một website để mọi người Việt đều có thể dễ dàng tìm được các thông tin về dịch vụ du lịch các nước trên toàn thế giới.\r\n\r\nVictoriatour đã tham gia vào các lĩnh vực khác như dịch vụ lưu trú, nhà hàng, dịch vụ phiên dịch và dịch thuật, phương tiện du lịch với mục tiêu là khách du lịch của chúng tôi có thể \'tận hưởng cuộc sống\'. Hệ thống của chúng tôi cung cấp các mức giá khác nhau với giá cả cạnh tranh để phù hợp với ngân sách của bạn và hỗ trợ bạn nhiều nhất có thể.\r\n\r\nVới tiêu chí luôn lấy trải nghiệm của khách hàng làm ưu tiên hàng đầu để ngày một hoàn thiện dịch vụ, Victoriatour đã trở thành một trong thương hiệu du lịch hàng đầu tại Việt Nam. Hãy đến với Victoria Tour để khám phá những điều kỳ diệu với phương châm: \"Enjoy life - Enjoy your life\".', 'Đã duyệt', 'logo_22_1748232913.webp', 'anh_bia_22_1748232958.webp', '100-499 nhân viên', 'kinh tế'),
(23, 'Công ty cổ phần Tập đoàn Danko', 'Tầng 1, nhà C6 – khối II – Đường Trần Hữu Dực - KĐT Mỹ Đình 1 – Nam Từ Liêm - Hà Nội.', '0987789654', 'Danko@gmail.com', '', 'Đã duyệt', '1747843400_logo_Công ty cổ phần Tập đoàn Danko.jpg', '1747843400_anhbia_bìa.jpg', '100', 'quản lý đất đai'),
(24, 'Anh Nguyen Co Ltd', '10A Ngõ 59 Quảng Khánh, Quảng An, Tây Hồ, Hà Nội', '0986654723', 'anhnguyen@gmail.com', 'Khởi đầu từ “Gia đình Đầu tư” (1988) theo mô hình doanh nghiệp gia đình.\r\nĐến năm 1998, thành lập công ty Anh Nguyễn và dần xây dựng được thương\r\nhiệu của mình trên thị trường BĐS Cư ngụ sinh thái (Second home)\r\n\r\nCông ty anh Nguyễn hoạt động trong lĩnh vực đầu tư và phát triển bất\r\nđộng sản được hơn 20 năm, chuyên về bất động sản nghỉ dưỡng.\r\n\r\nCam kết của Anh Nguyễn là luôn tạo ra các sản phẩm vừa thân thiện vừa độc\r\nđáo, chất lượng cao về kiến trúc và công năng, đáp ứng đầy đủ tiện ích tốt\r\nnhất cho khách hàng. Khách hàng, khi nhận một sản phẩm từ Anh Nguyễn,\r\nkhông chỉ là đầu tư để sở hữu một Bất động sản mà còn là hưởng thụ và sở\r\nhữu một PHONG CÁCH SỐNG.\r\n\r\nHiện chúng tôi đang có các dự án đầu tư tại Nha Trang, Ninh Bình, và tiến tới\r\nsẽ đầu tư tại Hà Nội, Tp HCM, Sapa,…', 'Đã duyệt', '1747843881_logo_Khởi đầu từ “Gia đình Đầu tư” (1988) theo mô hình doanh nghiệp gia đình..jpg', '1747843881_anhbia_bìa.jpg', '70', 'luật'),
(25, 'CÔNG TY TNHH ECOBA CÔNG NGHỆ MÔI TRƯỜNG', 'Tầng 5, Tòa nhà Udic Complex, Đường Hoàng Đạo Thúy, P. Trung Hòa, Q. Cầu Giấy, Hà Nội', '0989765642', 'ecoba@gmail.com', 'Hiện nay môi trường không chỉ là vấn đề được quan tâm hàng đầu của mỗi bộ, ban, ngành mà còn là vấn đề cấp thiết của mỗi doanh nghiệp sản xuất. Công ty TNHH Ecoba Công Nghệ Môi Trường (Ecoba ENT) tự hào là đơn vị có bề dày kinh nghiệm trên 10 năm trong lĩnh vực xử lý nước thải, xử lý nước cấp với hàng trăm công trình lớn nhỏ, công nghệ khác nhau và trải dài khắp đất nước. Chúng tôi mang dịch vụ sau bán hàng tốt nhất tới mọi khách hàng thông qua chính sách bảo hành tận tâm, chuyên nghiệp. Phương châm của chúng tôi không chỉ là chuyển giao cho Quý khách hàng một sản phẩm hoàn hảo, tiên tiến mà còn coi việc theo dõi giám sát chất lượng, duy trì hệ thống thiết bị vận hành ổn định, hoạt động lâu dài là mục tiêu hàng đầu và mang tính sống còn.', 'Đã duyệt', 'logo_25_1748233048.jpg', 'anh_bia_25_1748233048.jpg', '80', 'môi trường'),
(27, 'CÔNG TY TNHH SOCOTEC VIỆT NAM', '17 Ng. 575 P. Kim Mã, Ngọc Khánh, Ba Đình, Hà Nội', '0987865731', 'socotec@gmail.com', 'Socotec is a leading European group engaging in Building & Real Estate, Infrastructure, Industry & Equipment and Certification & Training.\r\n\r\nToday, Socotec operates in 26 countries and 5 continents, delivering highly specialized professional services to its clients, with the same high-quality standards across the globe.\r\n\r\nSocotec Vietnam established in 2018 and has been developing quickly as The Offshoring Platform Providing Engineering Capacity to the whole Socotec Group, across all international platforms.\r\n\r\nWe provide a wide technical portfolio covering the needs of Socotec companies, focusing on 4 business lines:\r\n\r\n-            MONITORING & DATA SERVICES\r\n\r\n-            ENGINEERING & SIMULATION\r\n\r\n-            BIM / CAD & AS-BUILT SURVEY\r\n\r\n-            SOFTWARE & IT SERVICES.\r\n\r\nSocotec Vietnam has been constantly acknowledged as one of the preferred employers in Vietnam, being certified with the Great Place to Work award in 2023 & 2024.\r\n\r\nWe are expanding our team to accommodate our fast-growing global client needs.\r\n\r\nJoin us in our mission to Build Trust For A Safer And Sustainable World', 'Đã duyệt', 'logo_27_1748233174.png', 'anh_bia_27_1748233174.jpg', '60', 'địa chất'),
(32, 'CÔNG TY CỔ PHẦN TẬP ĐOÀN VIDEC', 'Số 349 phố Vũ Tông Phan, Phường Khương Đình, Quận Thanh Xuân, Thành phố Hà Nội', '0987687656', 'videc@gmail.com', 'Tập đoàn VIDEC tiền thân là Công ty cổ phần tư vấn thiết kế và Xây dựng Việt Nam, Công ty cổ phần Đầu tư thiết kế và Xây dựng Việt Nam. Qua quá trình phát triển và trưởng thành, Công ty đã mở rộng lĩnh vực hoạt động kinh doanh và đổi tên thành Công ty cổ phần Tập đoàn VIDEC với các ngành nghề kinh doanh chính là đầu tư kinh doanh bất động sản, tư vấn thiết kế và thi công xây dựng cầu đường, khu công nghiệp… Trong đó lĩnh vực đầu tư kinh doanh bất động sản là lĩnh vực ưu tiên hàng đầu..', 'Đã duyệt', 'logo_32_1748231579.png', 'anh_bia_32_1748231579.jpg', '60', 'quản lý đất đai'),
(33, 'CÔNG TY CỔ PHẦN ĐẦU TƯ THIÊN ÂN', 'Tòa nhà XPhome Star - Khu đô thị Tân Tây Đô - Đan Phượng - Hà Nội', '0987865456', 'thienan@gmail.com', 'Công ty Cổ phần Đầu tư Thiên Ân được thành lập ngày 11/01/2010 theo giấy phép ĐKKD số 0104374560 do sở Kế hoạch và đầu tư thành phố Hà Nội cấp.\r\n-    Trụ sở chính: Tầng 2, toà nhà XPHOME STAR, KĐT, Tân Tây Đô, Đan Phượng, Hà Nội, Việt Nam\r\nCông ty Cổ phần Đầu tư Thiên Ân hoạt động trong lĩnh vực Tổng thầu và Đầu tư Xây dựng: Công trình Giao thông, Hạ tầng kỹ thuật, xây dựng Dân dụng và công nghiệp. Trải qua gần 10 năm hình thành và phát triển, tiếp nối tầm nhìn và đạo lý kinh doanh phát triển bền vững vì cộng đồng mà Người sáng lập đã đề ra, Thiên Ân luôn giữ vững tôn chỉ “Chất lượng – Uy tín – Nhân văn”.\r\nThiên Ân đã ghi dấu ấn trên thị trường với hàng loạt những Công trình và Dự án đầu tư tầm cỡ: \r\n•    Công trình thi công: Đài tưởng niệm Huyện Đan Phượng, Hà Nội; Tượng đài  cô gái 3 đảm Đang, thị trấn Phùng, Đan Phượng, Hà nội; Khách sạn Thiên Ân, Siêu Thị Thiên Ân, Bắc Giang…\r\n•    Dự án đầu tư: Khu dân cư mới Lạc Phú 3, TT Neo, huyện Yên Dũng, Bắc Giang; Dự án HH8 thuộc Khu đô thị phí Nam, thành phố Bắc Giang…\r\nBên cạnh lĩnh vực Tổng thầu và Đầu tư Xây dựng Thiên Ân còn mở rộng các lĩnh vực kinh doanh: Sản xuất Bao bì, Nội thất, Dệt may; Kinh doanh Dịch vụ; Thương mại; Sản xuất Nông nghiệp công nghệ cao', 'Đã duyệt', 'logo_33_1748233670.png', 'anh_bia_33_1748233670.jpg', '70', 'quản lý đất đai'),
(34, 'CÔNG TY CỔ PHẦN ĐẦU TƯ VĂN PHÚ - INVEST', 'Tòa nhà Văn Phú - Số 104 Thái Thịnh - Trung Liệt - Đống Đa - Hà Nội', '0678546823', 'vanphu@gmail.com', 'VĂN PHÚ INVEST – NƠI KHỞI NGUỒN CỦA GIẤC MƠ\r\n\r\nNếu bạn là người lao động đang mong muốn chuyển đổi một công việc tốt hơn hay bạn là sinh viên mới tốt nghiệp đang loay hoay lựa chọn một công việc phù hợp, hãy đến với VĂN PHÚ - INVEST của chúng tôi để biến những dự định và giấc mơ của bạn thành hiện thực.\r\n\r\nỞ Văn Phú, từ năm 2003 đã bắt đầu thực hiện giấc mơ và sứ mệnh cao cả là xây dựng những công trình thế kỷ để kiến tạo mái ấm cho các gia đình người Việt và tạo ra không gian sống tươi đẹp cho cộng đồng văn minh. Qua 16 năm trưởng thành, với phương châm “HỢP TÁC – PHÁT TRIỂN – BỀN VỮNG” chúng tôi đã không ngừng nỗ lực thúc đẩy sự phát triển của xã hội, chăm sóc cho các thế hệ tương lai và đạt được những thành tựu vượt trội.\r\n\r\n Trở thành công ty có uy tín và tiềm lực trong lĩnh vực đầu tư, xây dựng và phát triển đô thị tại Việt Nam.\r\n\r\n Đạt các Giải thưởng quốc tế BCI ASIA TOP 10, BCI ASIA AWARD, Top 50 nhãn hiệu nổi tiếng Việt Nam, Nhà phát triển Bất động sản tốt nhất Hà Nội.\r\n\r\n Ghi dấu ấn trên thị trường với những công trình, dự án tiêu biểu có quy mô đầu tư lên đến hàng chục ngàn tỷ đồng: khu đô thị mới Văn Phú, The Văn Phú - Victoria, Home city, The Terra - An Hưng, Hùng Sơn Villa, Giảng Võ Complex…\r\n\r\nTầm nhìn của chúng tôi là trở thành doanh nghiệp đi đầu trong lĩnh vực đầu tư – kinh doanh – phát triển hệ sinh thái bất động sản tầm trung tại Việt Nam và tạo dựng vị thế trong các lĩnh vực khác. Vì thế, chúng tôi tiếp tục mở rộng hợp tác với các đối tác trong và ngoài nước, tập trung phát triển nguồn nhân lực chất lượng cao, xây dựng thương hiệu vững mạnh.   \r\n\r\nĐến với Công ty Cổ phần Đầu tư Văn Phú - Invest, chúng tôi sẽ trao cho bạn điều gì?\r\n\r\n Một môi trường làm việc thân thiện, cởi mở với giá trị văn hóa đầy tính nhân văn, hợp tác và chia sẻ, kết nối tất cả thành viên trong tổ chức.\r\n\r\n Nhiều cơ hội thăng tiến nghề nghiệp; được hướng dẫn, đào tạo nâng cao kiến thức, kỹ năng và tạo mọi điều kiện phát triển.\r\n\r\n Chế độ lương thưởng, đãi ngộ hấp dẫn, cạnh tranh để khuyến khích và phát huy tối đa năng lực làm việc của nhân viên; tổ chức các chương trình du lịch, khám sức khỏe, ngày hội gia đình…. hàng năm và nhiều hoạt động giao lưu thú vị hàng tháng.\r\n\r\nĐừng bỏ lỡ cơ hội gia nhập một trong những doanh nghiệp bất động sản hàng đầu tại Hà Nội với nhiều vị trí tuyển dụng chất lượng và chính sách tương xứng đang chờ đón bạn.\r\n\r\n“Văn Phú – Invest, địa chỉ tin cậy để thực hiện giấc mơ của bạn”', 'Đã duyệt', 'logo_34_1748234125.png', 'anh_bia_34_1748234125.jpg', '60', 'thủy văn'),
(35, 'Công Ty Cổ Phần Tư Vấn Xây Dựng Và Thương Mại Nghi Tàm', 'Cầu Giấy, Hà Nội', '0988665542', 'nghitam@gmail.com', '', 'Đã duyệt', 'logo_35_1748234071.webp', 'anh_bia_35_1748234071.jpg', '60', 'thủy văn'),
(36, 'TỔNG CÔNG TY CỔ PHẦN THƯƠNG MẠI XÂY DỰNG', 'Số 201 Minh Khai, phường Minh Khai, quận Hai Bà Trưng, Hà Nội', '0678746531', 'thuongmaixaydung@gmail.com', 'Tổng Công ty Cổ phần Thương mại Xây dựng tiền thân là Nhà máy Vật liệu Hà Nội được thành lập từ năm 1961 thuộc Cục Cung cấp Vật tư, Bộ Giao thông Vận tải. Trải qua gần 60 năm hoạt động, Tổng Công ty Cổ phần Thương mại Xây dựng đã có những bước phát triển vượt bậc, trở thành một trong những tập đoàn kinh tế đa ngành hàng đầu tại Việt Nam, hoạt động trong các lĩnh vực Bất động sản; Sản xuất công nghiệp; Năng lượng và các ngành Dịch vụ khác. Đến nay, Tổng Công ty đã có hơn 1,500 cán bộ, công nhân viên tại trụ sở chính ở Hà Nội và các công trường ở Lào Cai, Nghệ An, Quảng Ngãi, TP HCM…', 'Đã duyệt', 'logo_36_1748233412.png', 'anh_bia_36_1748233337.jpg', '500', 'trắc địa bản đồ'),
(37, 'CÔNG TY CỔ PHẦN ĐẦU TƯ VÀNG PHÚ QUÝ', 'Số 30 Trần Nhân Tông , Phường Nguyễn Du, Quận Hai Bà Trưng, Hà Nội.', '0987865641', 'phuquy@gmail.com', 'Từ khi thành lập, Tập đoàn Vàng bạc đá quý Phú Quý đặt nền móng theo một phong cách hiện đại, sang trọng cùng những mẫu trang sức lộng lẫy và hoàn hảo. Đặt trụ sở chính tại con phố vàng – 30 Trần Nhân Tông. Phú Quý là thương hiệu quen thuộc với những khách hàng đam mê trang sức với những mẫu trang sức thực sự độc đáo và đẳng cấp. Trang sức Phú Quý hội tụ sự kết hợp hoàn hảo của phong cách, chất lượng và giá cả cùng với sự đa dạng của các mẫu trang sức mới tạo hứng khởi cho mỗi chuyến mua sắm của khách hàng. Những dòng sản phẩm chủ đạo: - Trang sức vàng cao cấp: Trang sức vàng 18K, 14K, 10K với thiết kế hiện đại, độc đáo và sang trọng; mẫu mã đa dạng, phong phú phù hợp với từng nhu cầu của Quý khách hàng. - Trang sức vàng 24K: chất lượng vàng đạt tiêu chuẩn quốc tế, phong phú về kiểu dáng, đa dạng về mẫu mã; luôn luôn được cập nhật theo xu hướng mới. Sản phẩm trang sức vàng 24K của Phú Quý không những có giá trị tích lũy, mà còn có giá trị về thẩm mỹ, làm đẹp. - Nhẫn cưới: Những BST nhẫn cưới Phú Quý được ra mắt thị trường từ năm 2012 ghi những dấu ấn sâu đậm về thương hiệu Phú Quý trong thị trường trang sức cưới. Đặc biệt , nhẫn cưới tại Phú Quý phong phú về màu sắc, không những chuyên về màu vàng truyền thống mà Phú Quý còn đưa vào thêm màu vàng trắng hiện đại, màu vàng hồng tinh tế, nhẫn cưới kết hợp ba màu độc đáo với các phiên bản 18K, 14K và 10K. Trong suốt nhiều năm, nhẫn cưới Phú Quý luôn được khách hàng yêu thích và là lựa chọn hàng đầu của các cặp đôi khi kết hôn. - Kim cương: Tập đoàn Vàng bạc đá quý PHÚ QUÝ đem đến sự hài lòng, thỏa mãn và khẳng định đẳng cấp cho các khách hàng đam mê dòng sản phẩm cao cấp này. Không gian mua sắm sang trọng, lịch sự với hàng trăm mẫu trang sức kim cương và kim cương viên tinh khiết, lộng lẫy, đa dạng về kích cỡ và thiết kế. Kim cương viên của Phú Quý tuân thủ các tiêu chuẩn về kim cương nghiêm ngặt và được chứng nhận bởi các tổ chức kiểm định uy tín trong nước và quốc tế như Viện Ngọc học số 1 thể giới của Mỹ: GIA. - Vàng mỹ nghệ 24K: Phú Quý tự hào khi luôn mang đến những sản phẩm hoàn hảo nhất để phục vụ Quý khách hàng và dòng sản phẩm Vàng mỹ nghệ 24K Phú Quý đã ra đời như thế. Với thiết kế độc đáo, tinh xảo cùng bàn tay của những nghệ nhân Phú Quý đã cho ra đời những sản phẩm tuyệt đẹp như bộ Lộc 12 con Giáp bằng vàng 24K – 999.9, bộ Tượng 12 con Giáp bằng vàng 24K – 999.9, Kim bài Thần Tài bằng vàng 24K – 999.9… Những sản phẩm này vô cùng phù hợp để làm món quà biếu tặng nhân những dịp trọng đại của Quý khách hàng.', 'Đã duyệt', 'logo_37_1748233516.jpg', 'anh_bia_37_1748233516.jpg', '100-499', 'địa chất'),
(38, 'CÔNG TY CỔ PHẦN UNIK XANH', 'Toà nhà TID, số 4 Liễu Giai, Ba Đình, Hà Nội', '0456783612', 'xanh@gmail.com', 'UNIK HOLDINGS GROUP là một tổ hợp các công ty hoạt động trong nhiều lĩnh vực như Đầu tư tài chính, Du lịch trải nghiệm,Du lịch nghĩ dưỡng, Dịch vụ hỗ trợ quản trị doanh nghiệp, Dịch vụ quản lý vận hành, Dịch vụ homecare, Dịch vụ bất động sản, thương mại,...Các công ty trên được tổ chức theo mô hình tập đoàn lấy Công ty đầu tư tài chính (UNIK HOLDINGS) làm công ty mẹ với vai trò quản lý vốn đầu tư tại các đơn vị thành viên thông qua các công cụ quản lý hệ thống tập trung như: quản trị chiến lược, quản trị nhân sự, quản trị rủi ro nhằm đảm bảo tính minh bạch về thông tin quản lý, hiệu quả trong sử dụng nguồn lực và có năng lực ứng phó với biến đổi của môi trường kinh doanh.\r\nCác công ty thành viên:\r\n\r\nCông ty Cổ phần Unik Xanh: Thực hiện đầu tư vào lĩnh vực du lịch với nhiệm vụ phát triển các sản phẩm du lịch nghỉ dưỡng sinh thái, du lịch trải nghiệm, du lịch sự kiện.\r\nCông ty TNHH MTV Dịch vụ tư vấn doanh nghiệp Unik: Thực hiện đầu tư vào lĩnh vực dịch vụ Tư vấn Quản trị doanh nghiệp có nhiệm vụ phát triển các sản phẩm về Tư vấn, Hỗ trợ Quản lý về CNTT, Quản lý Thông tin Doanh nghiệp, Quản lý Hợp đồng, Quản lý Tài chính, Quản lý Hành chính, Quản lý Chiến lược, Quản lý Truyền thông, Thương hiệu, Marketing.\r\nCông ty CP Unikare: Thực hiện đầu tư vào lĩnh vực dịch vụ quản lý vận hành, dịch vụ gia đình với nhiệm vụ phát triển các sản phẩm dịch vụ quản lý vận hành và Homecare.\r\nCông ty CP đầu tư xây dựng số 9 Hà Nội: Thực hiện đầu tư vào lĩnh vực bất động sản kinh doanh với nhiệm vụ khai thác cho thuê các mặt bằng đang hiện hữu, lập dự án đầu tư mới cho quỹ đất đang có …', 'Đã duyệt', 'logo_38_1748233640.jpg', 'anh_bia_38_1748233640.jpg', '24-99', 'tài nguyên nước'),
(39, 'TRUNG TÂM CHẤT LƯỢNG VÀ BẢO VỆ TÀI NGUYÊN NƯỚC', 'Số 93, ngõ 95 đường Vũ Xuân Thiều - Phường Sài Đồng - Quận Long Biên - Hà Nội.', '0967854612', 'tainguyennuoc@gmail.com', 'Trung tâm Chất lượng và Bảo vệ tài nguyên nước là đơn vị sự nghiệp công lập trực thuộc Trung tâm Quy hoạch và Điều tra tài nguyên nước quốc gia - Bộ Tài nguyên và Môi trường, có chức năng điều tra, đánh giá chất lượng nguồn nước; phân tích, thí nghiệm chất lượng nước; xử lý, cải tạo, phục hồi và bảo vệ các nguồn nước; cung cấp các dịch vụ công về tài nguyên nước theo quy định của pháp luật.', 'Đã duyệt', 'logo_39_1748233421.webp', 'anh_bia_39_1748233322.jpg', '25-99', 'tài nguyên nước'),
(40, 'Ngân Hàng TMCP Quân Đội', 'MB Tower, 18 Lê Văn Lương, Trung Hòa, Cầu Giấy, Hà Nội', '0679327433', 'mb@gmail.com', 'MB là một định chế vững về tài chính, mạnh về quản lý, minh bạch về thông tin, thuận tiện và tiên phong trong cung cấp dịch vụ để thực hiện được sứ mệnh của mình, là một tổ chức, một đối tác Vững vàng, tin cậy.\r\n\r\nTrong suốt quá trình hình thành và phát triển, dưới sự lãnh đạo, chỉ đạo của Quân ủy Trung ương - Bộ Quốc phòng, Ngân hàng nhà nước và sự hỗ trợ, giúp đỡ tận tình của các cơ quan hữu quan; đơn vị trong và ngoài quân đội; Ngân hàng TMCP Quân Đội (MB) đã phát huy bản chất tốt đẹp và truyền thống vẻ vang của người chiến sỹ trên mặt trận kinh tế; đoàn kết, chủ động, sáng tạo, tự lực, tự cường, khắc phục khó khăn, cải tiến chất lượng hoạt động đưa các sản phẩm dịch vụ Ngân hàng tốt nhất đến với các cá nhân, tổ chức kinh tế, các doanh nghiệp trên khắp các tỉnh, thành trọng điểm của cả nước, góp phần đẩy mạnh công cuộc phát triển kinh tế của Việt Nam nói chung và nâng cao hiệu quả kinh doanh của ngành Ngân hàng nói riêng.', 'Đã duyệt', 'logo_40_1748233274.png', 'anh_bia_40_1748233274.png', '500', 'công nghệ thông tin'),
(41, 'Công ty Cổ phần IntelLife', 'Tháp B, tòa Fafim, số 19 Nguyễn Trãi, Thanh Xuân, HN', '0458768322', 'intelLife@gmail.com', 'Chúng tôi là TokyoLife (https://tokyolife.vn/) - Chuỗi bán lẻ đồ gia dụng, hóa mỹ phẩm, phụ kiện chính hãng các thương hiệu Nhật Bản, phụ kiện giày, túi, ví, balo và thời trang thương hiệu TokyoLife, TokyoBasic, In The Now và nhiều thương hiệu thời trang, phụ kiện khác sản xuất tại Việt Nam, Trung Quốc, Thái Lan…', 'Đã duyệt', 'logo_41_1748625838.png', 'anh_bia_41_1748625838.jpg', '500', 'công nghệ thông tin'),
(42, 'CÔNG TY CỔ PHẦN QUẢN LÝ KHÁCH SẠN VÀ KHU NGHỈ DƯỠNG LYNN TIMES THUỘC TẬP ĐOÀN ONSEN FUJI', 'Số 2 Ngõ 95 Chùa bộc, Đống đa, Hà Nội', '0456873521', 'onsen@gmail.com', 'Công ty cổ phần quản lý Khách sạn và Khu nghỉ dưỡng Lynn Times thuộc Tập đoàn Onsen Fuji. Tập đoàn Onsen Fuji là doanh nghiệp hàng đầu trong lĩnh vực đầu tư, phát triển bất động sản tại Việt Nam, chuyên kiến tạo những dự án hạng sang gắn với các thương hiệu quốc tế, tiên phong mang đến trải nghiệm chất lượng, khác biệt cho khách hàng, góp phần sinh lời hiệu quả và gia tăng giá trị cho nhà đầu tư. Tập đoàn hiện là đối tác tin cậy, uy tín của các Công ty, tổ chức trong và ngoài nước.\r\n\r\nCông ty cổ phần quản lý Khách sạn và Khu nghỉ dưỡng Lynn Times  được thành lập vào ngày 04/11/2021, với mục tiêu trở thành đơn vị dẫn đầu trong lĩnh vực quản lý và vận hành các khách sạn và khu nghỉ dưỡng tại Việt Nam. Với sứ mệnh đem đến những trải nghiệm nghỉ dưỡng tuyệt vời và dịch vụ hoàn hảo cho khách hàng, chúng tôi luôn nỗ lực không ngừng để đạt được điều đó.\r\n\r\nNgành nghề hoạt động chính của chúng tôi là dịch vụ lưu trú ngắn ngày, bao gồm các dịch vụ như đặt phòng khách sạn, quản lý và vận hành khu nghỉ dưỡng, cung cấp các tiện ích và dịch vụ đi kèm. Chúng tôi cam kết mang đến cho khách hàng những trải nghiệm nghỉ dưỡng đẳng cấp và không gian sống lý tưởng.', 'Đã duyệt', 'logo_42_1748625809.jpg', 'anh_bia_42_1748625809.jpg', '500-999', 'công nghệ thông tin'),
(43, 'Trường Đại Học FPT', 'Trường Đại học FPT, Khu công nghệ cao Hòa Lạc, Thạch Hòa, Thạch Thất, Hà Nội', '0457362191', 'fpt@gmail.com', '', 'Đã duyệt', 'logo_43_1748233353.webp', 'anh_bia_43_1748233353.webp', '50', 'lý luận chính trị'),
(44, 'Trường Đại học CMC', 'Tây Mỗ - Phường Tây Mỗ - Quận Nam Từ Liêm - Hà Nội.', '0322098761', 'cmc@gmail.com', 'Thành lập năm 2011, Trường Đại học Mỹ thuật Công nghiệp Á Châu (tiền thân của Trường Đại học CMC) là một trong hai trường đại học trong cả nước chuyên đào tạo cử nhân mỹ thuật công nghiệp hệ chính quy. Năm 2021, Trường đã có bước phát triển đột phá khi tiếp nhận đầu tư chiến lược từ Tập đoàn Công nghệ CMC. Căn cứ Quyết định số 895/QĐ-TTg của Thủ tướng Chính phủ, Trường Đại học Mỹ thuật Công nghiệp Á Châu chính thức đổi tên thành Trường Đại học CMC kể từ ngày 26/07/2022.\r\n\r\nLà một thành viên thuộc Khối Giáo dục và Nghiên cứu của Tập đoàn Công nghệ CMC, Trường Đại học CMC được định hướng phát triển từ “Đại học thông minh, đổi mới sáng tạo” trong giai đoạn 2022 – 2032, tới “Đại học nghiên cứu” (World class university) sau năm 2032. Mục tiêu hàng đầu của Nhà trường là trở thành một Đại học công nghệ với các lĩnh vực đào tạo thế mạnh là Khoa học máy tính, hệ thống thông tin, điện tử – viễn thông và các lĩnh vực khác gắn với yêu cầu phát triển khoa học công nghệ và kinh tế số của cả nước, với quy mô cỡ trung từ 20 đến 30 nghìn sinh viên vào năm 2039.\r\n\r\nVới khoản đầu tư hơn một nghìn tỉ đồng của giai đoạn một, Trường Đại học CMC ưu tiên nâng cấp cơ sở vật chất hiện đại, mở các ngành đào tạo mới, xây dựng đội ngũ giảng viên trình độ cao và hỗ trợ sinh viên giỏi. Trường Đại học CMC là một môi trường học thuật tiên tiến, lấy người học làm trung tâm, gắn học với hành, giúp sinh viên trưởng thành nhanh qua thực tiễn, trở thành con người hành động và chủ thể của sáng tạo. Sinh viên của Trường có cơ hội thực tập tại các doanh nghiệp công nghệ hàng đầu ở trong nước, Nhật Bản, Hàn Quốc, Hoa Kỳ,… Việc học tập tại Trường cũng mở ra cơ hội việc làm tại Tập đoàn CMC, Tập đoàn Samsung và các đối tác công nghệ toàn cầu của CMC.', 'Đã duyệt', 'logo_44_1748233367.webp', 'anh_bia_44_1748233367.webp', '100-399', 'khoa học đại cương'),
(45, 'Ngân Hàng TMCP Việt Nam Thịnh Vượng - VPBANK', '89 Láng Hạ, Quận Đống Đa, Hà Nội', '0348765411', 'vpbank@gmail.com', 'Chào mừng Bạn đến với Miền Đất Nhân Tài!\r\n\r\nTrong chiến lược phát triển 5 năm lần thứ 3 (2022-2026), VPBank xác định mục tiêu trở thành ngân hàng có vị trí vững chắc trong Top 3 Ngân hàng lớn nhất Việt Nam và đạt quy mô thuộc Top 100 ngân hàng lớn nhất châu Á, góp phần thúc đẩy sự phát triển bền vững và thịnh vượng của quốc gia và cộng đồng. Một yếu tố quan trọng để hiện thực hóa mục tiêu này chính là Con người và VPBank đã luôn tập trung vào chiến lược “Home of Talents” – xây dựng VPBank thực sự là miền đất thu hút và gắn kết các nhân tài hàng đầu trên thị trường.\r\n\r\nTrên hành trình hiện thực hóa sứ mệnh vì một Việt Nam thịnh vượng, VPBank tập trung nỗ lực xây dựng một hình mẫu VPBanker lý tưởng với 04 phương diện phát triển toàn diện gồm: Thịnh vượng Thân trí, Thịnh vượng Tinh thần, Thịnh vượng Tài chính và Thịnh vượng Cộng đồng. Đây là sự cam kết từ VPBank trong chiến lược Home of Talents - đặt Con người làm trung tâm và không ngừng nỗ lực tạo những điều kiện thuận lợi để từng nhân viên có thể phát triển tốt nhất cùng ngân hàng. Đây cũng là một phần của chiến lược bền vững đưa VPBank trở thành doanh nghiệp kiểu mẫu đáng tin cậy, xây dựng một môi trường làm việc hiệu quả, nhân văn, thu hút nhân tài và phát triển mỗi VPBanker trở thành phiên bản tốt nhất của chính mình.\r\n\r\n1. Thịnh vượng Thân trí: Hai yếu tố “trí tuệ” và “thể chất” luôn song hành và bổ trợ cho nhau trong chân dung Con người Thịnh vượng. VPBank luôn chú trọng đến việc:\r\n\r\n•      Xây dựng một môi trường làm việc lành mạnh\r\n\r\n•      Chăm sóc sức khỏe, khích lệ tinh thần luyện tập thể thao của CBNV\r\n\r\n•      Đề cao văn hóa học tập, phát triển kỹ năng, cổ vũ và hỗ trợ cho sự học trọn đời của từng nhân viên.\r\n\r\n2. Thịnh vượng Tinh thần: VPBank ý thức được tầm quan trọng của đời sống tinh thần, sức mạnh của những suy nghĩ tích cực và lợi ích của sự cân bằng về cảm xúc đối với từng nhân viên. VPBank tập trung nỗ lực để:\r\n\r\n•      Kiến tạo môi trường làm việc công bằng, cởi mở, nhân văn.\r\n\r\n•      Đồng hành và chia sẻ với CBNV thông qua chính sách, chương trình chăm sóc CBNV và người thân.\r\n\r\n3. Thịnh vượng Tài chính: VPBank ý thức rằng một nền tảng tài chính vững chắc sẽ mang lại chất lượng cuộc sống tốt hơn cho các VPBanker. VPBank luôn đảm bảo:\r\n\r\n•      Xây dựng chính sách về lương, thưởng phúc lợi, lộ trình thăng tiến rõ ràng, hấp dẫn.\r\n\r\n•      Ghi nhận xứng đáng mọi nỗ lực và cống hiến của CBNV.\r\n\r\n4. Thịnh vượng Cộng đồng: Mỗi VPBanker là một phần của xã hội, những đóng góp, nỗ lực của chính bản thân các VPBanker sẽ góp phần thay đổi và tạo nên một xã hội tốt đẹp hơn. Từ phía tổ chức, VPBank luôn xem trọng và hành động để thực hiện:\r\n\r\n•      Trách nhiệm của doanh nghiệp với cộng đồng và xã hội.\r\n\r\n•      Thường xuyên thực hiện những chương trình, sáng kiến nhằm góp phần giúp cho xã hội, đất nước ngày một giàu đẹp, văn minh hơn.', 'Đã duyệt', 'logo_45_1748233289.png', 'anh_bia_45_1748233289.png', '10.000-19.999', 'luật'),
(46, 'Công Ty Cổ Phần Đầu Tư Thương Mại Quốc Tế Mặt Trời Đỏ', 'Tầng 25 tòa nhà Handico, Số 3 Mễ Trì Hạ, Nam Từ Liêm, Hà Nội', '0459876461', 'mattroido@gmail.com', 'Công Ty Cổ Phần Đầu Tư Thương Mại Quốc Tế Mặt Trời Đỏ (REDSUN ITI CORP) là một trong số các công ty hàng đầu tại Việt Nam trong lĩnh vực kinh doanh nhà hàng ẩm thực. Redsun hiện có hơn 5000 cán bộ nhân viên làm việc tại 2 thành phố lớn là Hà Nội và Hồ Chí Minh. \r\nRedsun ITI sở hữu trên 13 thương hiệu ẩm thực nổi tiếng, phát triển thành các chuỗi nhà hàng tại Hà Nội, TP. Hồ Chí Minh và các tỉnh thành trên cả nước: \r\n1. ThaiExpress - Chuỗi nhà hàng Thái hiện đại lớn nhất thế giới \r\nThaiExpress là thương hiệu đã được công nhận và ưa chuộng, có mặt tại 5 quốc gia. Redsun mang ThaiExpress đến với Việt Nam từ năm 2009, đến nay đã có trên 10 nhà hàng. \r\n2. Seoul Garden – Buffet nướng và lẩu Quốc tế \r\nSeoul Garden hiện có hơn 30 nhà hàng trên toàn thế giới. Redsun mang SEOul Garden đến Việt Nam từ năm 2009, đến nay đã có trên 5 nhà hàng. \r\n3. King BBQ – Vua nướng Hàn Quốc. \r\nKing BBQ là chuỗi nhà hàng nướng Hàn Quốc được phát triển bởi Redsun ITI, có trên 20 nhà hàng trên cả nước. \r\n4. Capricciosa – Nhà hàng Ý phong cách Napoli. \r\nCapricciosa là chuỗi nhà hàng Ý truyền thống được yêu thích trên toàn thế giới, tính đến nay đã có hơn 145 nhà hàng Capricciosa ở nhiều quốc gia. \r\n5. Tasaki - Chuỗi nhà hàng nướng Nhật Bản \r\nTasaki là nhà hàng nướng phong cách Nhật được phát triển bởi Redsun, hiện đã có mặt tại TP. Hồ Chí Minh. \r\n6. Hotpot Story – Buffet lẩu Thái, Nhật, Hàn, Trung \r\nHotpot Story là chuỗi nhà hàng lẩu theo phong cách buffet đầu tiên tại Việt Nam được sáng tạo và phát triển bởi Redsun, hiện đã có gần 10 nhà hàng trên cả nước. \r\n7. Sushi Kei – Nhà hàng Nhật Bản \r\nSushi Kei là chuỗi nhà hàng Nhật được sáng tạo và phát triển bởi Redsun, hiện có mặt tại Hà Nội và TP. Hồ Chí Minh. \r\n8. Buk Buk – Quán nhậu chuẩn Hàn Quốc \r\nBuk Buk là quán nhậu theo phong cách Hàn Quốc được sáng tạo và phát triển bở Redsun, hiện có mặt tại Hà Nội và TP. Hồ Chí Minh. \r\n9. Khao Lao – Nhà hàng Lào \r\nKhao Lao là chuỗi nhà hàng ẩm thực Lào được sáng tạo và phát triển bởi Redsun, hiện có mặt tại Hà Nội và TP. Hồ Chí Minh \r\n10. Dolpan Sam – nướng đá Hàn Quốc \r\nĐây là thương hiệu Redsun nghiên cứu và cho ra đời theo mô hình nướng bàn đá đúng chuẩn Hà Quốc \r\n11. Meiwei - ẩm thực Trung Hoa \r\nChuỗi nhà hàng Dimsum và hải sản ra đời với mong muốn mang đến cho quý thực khách cảm nhận những nét tuyệt hảo nhất của ẩm thực Trung Hoa đậm tình, đậm vị. \r\n12. Truly Việt - ẩm thực Việt truyền thống \r\nTruly Việt là nơi lưu giữ nguyên vẹn giá trị và vẻ đẹp của ẩm thực Việt truyền thống. \r\n13. Octopus King - Nhà hàng vua bạch tuộc \r\nMột phong cách nhà hàng hoàn toàn mới lạ với mô hình XÀO – NƯỚNG – LẨU và chuyên món Bạch tuộc đầu tiên và lớn nhất tại Việt Nam. \r\n\r\nVới triết lý kinh doanh “Tất cả vì lợi ích khách hàng”, Redsun đầu tư tất cả cho con người và công nghệ nhằm không ngừng nâng cao chất lượng sản phẩm và dịch vụ để tối đa sự hài lòng của khách hàng – nền tảng của kinh doanh bền vững. Redsun đã và đang bước từng bước phát triển vững chắc, không ngừng mở rộng quy mô, số lượng thương hiệu và nhà hàng mới. \r\nTrên đà phát triển không ngừng, Redsun là môi trường làm việc lý tưởng của những ngời yêu thích ngành F&B. Redsun ưu tiên tuyển dụng ứng viên có tinh thần đương đầu thử thách, tích cực học hỏi và không ngừng phát triền bản thân, thúc đẩy sự phát triển chung của công ty. Đến với chúng tôi, các bạn sẽ có cơ hội được làm việc trong môi trường lý tưởng, với mức lương hấp dẫn và các cơ hội thăng tiến tùy theo năng lực.', 'Đã duyệt', 'logo_46_1748233695.jpg', 'anh_bia_46_1748233695.jpg', '300', 'luật'),
(47, 'Công ty CP Tư vấn Năng lượng và Môi trường', 'Tòa Diamond Flower, số 48 Lê Văn Lương, Thanh Xuân, Hà Nội', '0346579811', 'nangluongmoitruong@gmail.com', 'Công ty Cổ phần Tư vấn Năng lượng và Môi trường (VNEEC) được thành lập vào tháng 9 năm 2006 tại Hà Nội, Việt Nam. Tập đoàn South Pole (Thụy Sỹ) là một trong ba cổ đông chính của công ty, đồng thời cũng là một đối tác chiến lược trong suốt quá trình phát triển của VNEEC từ khi thành lập cho đến nay.\r\n\r\nVới đội ngũ chuyên gia trong nước và quốc tế có năng lực và kiến thức chuyên sâu, VNEEC cung cấp các dịch vụ về dự án tín chỉ carbon, tư vấn chính sách, bảo vệ tầng ozone, tư vấn doanh nghiệp và xúc tiến đầu tư. VNEEC luôn hỗ trợ với sự chuyên nghiệp cao nhất để giúp khách hàng đạt được mục tiêu trong các hoạt động ứng phó với biến đổi khí hậu.\r\n\r\nVNEEC luôn mở rộng quy mô và chất lượng dịch vụ với các hoạt động trọng tâm:\r\n\r\nTư vấn, xây dựng và quản lý dự án tín chỉ carbon trong các lĩnh vực khác (tiết kiệm năng lượng, xử lý rác thải, xử lý nước thải, phát triển nguồn nước sạch, chuyển đổi nhiên liệu, trồng rừng – tái trồng rừng & REEDs);\r\n\r\nTư vấn ban hành tín chỉ carbon và tín chỉ năng lượng tái tạo;\r\n\r\nThực hiện các dự án/ hoạt động tư vấn trong nước và quốc tế liên quan đến chính sách và chiến lược quốc gia, ngành về:\r\n\r\n– ứng phó với biến đổi khí hậu và phát triển thị trường carbon;\r\n\r\n– về bảo vệ tầng ozone;\r\n\r\n– tăng trưởng xanh & phát triển bền vững;\r\n\r\n– năng lượng tái tạo & tiết kiệm năng lượng.\r\n\r\nTư vấn về kiểm kê khí nhà kính, tính toán dấu chân carbon và xây dựng kế hoạch giảm nhẹ phát thải khí nhà kính cho doanh nghiệp và các quỹ đầu tư.', 'Đã duyệt', 'logo_47_1748233467.webp', 'anh_bia_47_1748233467.jpg', '10-24', 'biến đổi khí hậu'),
(48, 'CÔNG TY TNHH SOLPAC VIỆT NAM', 'Tầng 7, Tòa nhà 170 đường Trần Duy Hưng, Phường Trung Hòa, Quận Cầu Giấy, Thành phố Hà Nội, Việt Nam', '0458763567', 'SOLPAC@gmail.com', 'Cung cấp các đề xuất tối ưu và hệ thống phần mềm có giá trị để thúc đẩy chuyển đổi số cho các nhà máy sản xuất Nhật Bản tại Việt Nam', 'Đã duyệt', 'logo_48_1748233239.webp', 'anh_bia_48_1748233239.jpg', '10-24', 'ngoại ngữ'),
(49, 'Công ty Cổ phần Bông Sen', 'B1709 MATRIX ONE -NAM TỪ LIÊM - HÀ NỘ', '0568793442', 'bongsen@gmail.com', '', 'Đã duyệt', '1747856494_logo_Công ty Cổ phần Bông Sen.webp', '1747856494_anhbia_bìa.jpg', '25-99', 'ngoại ngữ'),
(50, 'Công ty TNHH kỹ thuật công trình Tân Khoa', 'Phòng 1908, Tòa nhà Charmvit Tower, Số 117 Trần Duy Hưng, Phường Trung Hoà, Quận Cầu Giấy, Thành phố Hà Nội', '0459872341', 'tankhoa@gmail.com', 'Công ty TNHH Kỹ thuật Công trình Tân Khoa thành lập năm 2013, là đơn vị tổng thầu các công trình bảo vệ môi trường với quy mô lớn. Công ty chuyên nghiên cứu, phát triển, thiết kế, chế tạo, lắp đặt, chạy thử, vận hành các công trình phòng chống ô nhiễm không khí, các công trình xử lý nước, công trình hệ thống băng tải. Trên cơ sở hợp tác với các viện thiết kế quy mô lớn của Trung Quốc, áp dụng công nghệ tiên tiến hàng đầu thế giới, kết hợp với đội ngũ thiết kế ưu tú của công ty tại Việt Nam, Tân Khoa đang trên đà phát triển nhanh chóng, gặt hái được những thành tựu đáng tự hào tại thị trường Việt Nam.\r\n\r\nTân Khoa hiện có tổng số hơn 120 cán bộ công nhân viên. Ban lãnh đạo Tân Khoa luôn chú trọng việc đầu tư vào giá trị con người, tạo những điều kiện thuận lợi nhất và xây dựng lộ trình công danh rõ ràng giúp cho nhân viên phát huy tối đa tiềm năng của bản thân, đạt được những thành quả xứng đáng với năng lực. Tại Tân Khoa, chúng ta sẽ:\r\n\r\nCùng nhau nỗ lực – kiến tạo thành công:\r\n- Ở Tân Khoa, mỗi cá nhân đều là 1 mảnh ghép quan trọng\r\n- Khuyến khích nhân viên chủ động nêu lên quan điểm, ý kiến cá nhân\r\n- Khuyến khích sự tự quản lý, làm chủ dự án và công việc của bản thân\r\n\r\nPhúc lợi đa dạng: \r\n- Thưởng hiệu suất, thưởng tháng lương 13, thưởng hiệu quả kinh doanh cuối năm... \r\n- Review tăng lương mỗi năm 1 lần\r\n- Các gói bảo hiểm tai nạn cho nhân viên làm việc tại công trường...\r\n- Hỗ trợ ăn trưa, gửi xe…\r\n- Thưởng thâm niên\r\n- Phụ cấp con nhỏ 500.000/ tháng cho tới khi đủ 6 tuổi\r\n\r\nKhuyến khích tinh thần học hỏi và giao lưu văn hoá:\r\n- Thường xuyên tổ chức các chương trình đào tạo nâng cao thái độ, kiến thức, kỹ năng\r\n- Team building hàng quý, du lịch hàng năm\r\n- Year end party\r\n- Các ngày lễ kỉ niệm 8/3, Tết thiếu nhi 1/6, 20/10...\r\n\r\nHoan nghênh và chào đón bạn tìm hiểu các cơ hội nghề nghiệp tại Tân Khoa. Chúng tôi luôn mong muốn tìm kiếm những người giàu nhiệt huyết, đam mê thử thách để cùng đồng hành và kiến tạo nên những giá trị bền vững, lý tưởng cho cá nhân, cho tổ chức và cho cộng đồng. Hãy cùng tìm hiểu và gia nhập đội ngũ Tân Khoa qua các cơ hội việc làm dưới đây bạn nhé!', 'Đã duyệt', 'logo_50_1748233103.webp', 'anh_bia_50_1748233103.jpg', '100-499', 'môi trường'),
(51, 'CÔNG TY CỔ PHẦN CHUYỂN ĐỔI SỐ AIONTECH', 'Hà Nội: 106 Hoàng Quốc Việt, P.Nghĩa Tân, Cầu Giấy', '0469837825', 'AIONTECH@gmail.com', 'AIONtech là doanh nghiệp tiên phong về AIOT (Artificial Intelligence of Things – trí tuệ nhân tạo vạn vật) để tạo ra các giải pháp chuyển đổi số đột phá cho doanh nghiệp Việt Nam như: thanh toán bằng khuôn mặt, trải nghiệm khách hàng thông minh, kiosk tự phục vụ…\r\n\r\nVới công nghệ Trí tuệ nhân tạo Made in Vietnam cùng các thiết bị thông minh iOT, chúng tôi tự hào được đồng hành và triển khai nhiều dự án chuyển đổi số cho các tập đoàn và doanh nghiệp hàng đầu như: VinGroup, Trung Nguyên, Mobifone, Viettel …', 'Đã duyệt', '1747857162_logo_CÔNG TY CỔ PHẦN CHUYỂN ĐỔI SỐ AIONTECH.webp', '1747857162_anhbia_CÔNG TY CỔ PHẦN CHUYỂN ĐỔI SỐ AIONTECH.webp', '25-99', 'kinh tế');

-- --------------------------------------------------------

--
-- Table structure for table `co_so_thuc_tap`
--

CREATE TABLE `co_so_thuc_tap` (
  `stt_cstt` int(11) NOT NULL,
  `ma_co_so` varchar(50) NOT NULL,
  `ten_co_so` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('co_so_thuc_tap') NOT NULL DEFAULT 'co_so_thuc_tap'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `co_so_thuc_tap`
--

INSERT INTO `co_so_thuc_tap` (`stt_cstt`, `ma_co_so`, `ten_co_so`, `email`, `password`, `role`) VALUES
(4, '123123', 'co_so_thuc_tap', 'cstt123@gmail.com', '$2y$10$jbRk.8JbenOODm/chE7mZueYZkiDiM7SYB/3A32ZhQY4TwMSfC8/a', 'co_so_thuc_tap');

-- --------------------------------------------------------

--
-- Table structure for table `danh_gia_thuc_tap`
--

CREATE TABLE `danh_gia_thuc_tap` (
  `stt_danhgia` int(11) NOT NULL,
  `ma_dang_ky` int(11) NOT NULL,
  `stt_sv` int(11) NOT NULL,
  `stt_cstt` int(11) NOT NULL,
  `ten_co_so` varchar(255) NOT NULL,
  `tieu_de_tuyen_dung` varchar(255) NOT NULL,
  `cong_ty` varchar(255) NOT NULL,
  `email_lien_he` varchar(100) NOT NULL,
  `giang_vien_huong_dan` varchar(100) NOT NULL,
  `ma_sinh_vien` varchar(50) NOT NULL,
  `lop_khoa` varchar(100) NOT NULL,
  `nganh_hoc` varchar(50) NOT NULL,
  `thoi_gian_thuc_tap` varchar(100) NOT NULL,
  `thai_do` enum('Xuất sắc','Tốt','Trung bình','Yếu') NOT NULL,
  `thai_do_ghi_chu` text DEFAULT NULL,
  `ky_nang_chuyen_mon` enum('Xuất sắc','Tốt','Trung bình','Yếu') NOT NULL,
  `ky_nang_ghi_chu` text DEFAULT NULL,
  `lam_viec_nhom` enum('Xuất sắc','Tốt','Trung bình','Yếu') NOT NULL,
  `lam_viec_nhom_ghi_chu` text DEFAULT NULL,
  `ky_nang_giao_tiep` enum('Xuất sắc','Tốt','Trung bình','Yếu') NOT NULL,
  `ky_nang_giao_tiep_ghi_chu` text DEFAULT NULL,
  `thich_nghi` enum('Xuất sắc','Tốt','Trung bình','Yếu') NOT NULL,
  `thich_nghi_ghi_chu` text DEFAULT NULL,
  `tuan_thu` enum('Xuất sắc','Tốt','Trung bình','Yếu') NOT NULL,
  `tuan_thu_ghi_chu` text DEFAULT NULL,
  `nhan_xet_chung` text DEFAULT NULL,
  `ket_qua_de_xuat` varchar(255) DEFAULT NULL,
  `ngay_danh_gia` date NOT NULL,
  `nguoi_danh_gia` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `danh_gia_thuc_tap`
--

INSERT INTO `danh_gia_thuc_tap` (`stt_danhgia`, `ma_dang_ky`, `stt_sv`, `stt_cstt`, `ten_co_so`, `tieu_de_tuyen_dung`, `cong_ty`, `email_lien_he`, `giang_vien_huong_dan`, `ma_sinh_vien`, `lop_khoa`, `nganh_hoc`, `thoi_gian_thuc_tap`, `thai_do`, `thai_do_ghi_chu`, `ky_nang_chuyen_mon`, `ky_nang_ghi_chu`, `lam_viec_nhom`, `lam_viec_nhom_ghi_chu`, `ky_nang_giao_tiep`, `ky_nang_giao_tiep_ghi_chu`, `thich_nghi`, `thich_nghi_ghi_chu`, `tuan_thu`, `tuan_thu_ghi_chu`, `nhan_xet_chung`, `ket_qua_de_xuat`, `ngay_danh_gia`, `nguoi_danh_gia`) VALUES
(8, 14, 67, 4, 'SOLIS LAB', 'Mid. Frontend Dev - HTML5/CSS3/JavaScript', 'SOLIS LAB', 'info@solislab.com', 'NGÔ THẾ ANH', '1234567892', 'a3 - bo_mon_luat', 'bo_mon_luat', '2/2/2025', 'Xuất sắc', '', 'Xuất sắc', '', 'Xuất sắc', '', 'Xuất sắc', '', 'Xuất sắc', '', 'Xuất sắc', '', '123', NULL, '2025-05-16', 'Cơ sở thực tập'),
(10, 19, 77, 4, 'CÔNG TY TNHH ECOBA CÔNG NGHỆ MÔI TRƯỜNG', 'Kỹ Sư Dự Án Môi Trường', 'CÔNG TY TNHH ECOBA CÔNG NGHỆ MÔI TRƯỜNG', 'ecoba@gmail.com', 'NGUYỄN KHẮC THÀNH', '22111061353', 'moitruong - moi_truong', 'moi_truong', '6/3/2025  6/3/2035', 'Xuất sắc', '', 'Xuất sắc', '', 'Xuất sắc', '', 'Xuất sắc', '', 'Xuất sắc', '', 'Xuất sắc', '', 'Đạt', 'Đạt yêu cầu', '2025-06-03', 'Cơ sở thực tập'),
(11, 20, 64, 4, 'CÔNG TY LUẬT TNHH ĐẦU TƯ QUỐC TẾ AN PHÁT', 'Kế Toán Thuế', 'CÔNG TY LUẬT TNHH ĐẦU TƯ QUỐC TẾ AN PHÁT', 'anphat@gmail.com', 'NGÔ THẾ ANH', '1234567890', 'a3 - bo_mon_luat', 'bo_mon_luat', '6/3/2025', 'Xuất sắc', '', 'Xuất sắc', '', 'Tốt', '', 'Tốt', '', 'Xuất sắc', '', 'Xuất sắc', '', 'TỐT', 'Đạt yêu cầu,Đề nghị khen thưởng', '2025-06-03', 'Cơ sở thực tập');

-- --------------------------------------------------------

--
-- Table structure for table `giang_vien`
--

CREATE TABLE `giang_vien` (
  `stt_gv` int(11) NOT NULL,
  `so_hieu_giang_vien` varchar(50) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `khoa` varchar(50) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `role` enum('giang_vien') NOT NULL DEFAULT 'giang_vien'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `giang_vien`
--

INSERT INTO `giang_vien` (`stt_gv`, `so_hieu_giang_vien`, `ho_ten`, `email`, `password`, `khoa`, `so_dien_thoai`, `role`) VALUES
(12, '2223456798', 'Lê Lan Anh', 'llanh@hunre.edu.vn', '$2y$10$4zcwR8Elfc2x3f0JZC9Gzut7J2xSVZrgR.26mBW9vzN1vWy2mlmyy', 'cntt', '0987654335', 'giang_vien'),
(13, '2438370598', 'TRẦN VĂN HẢI', 'Haitv2008@gmail.com', '$2y$10$HnCyZdCxAb9XkUprfSL74OaNi53n0flapmDuwg0LhVtIueSEHUeqG', 'kinh_te', '0917654335', 'giang_vien'),
(14, '9120403251', 'NGUYỄN KHẮC THÀNH', 'nkthanh@hunre.edu.vn', '$2y$10$tPjmOOqQdNQAmaKptotzR.lZIDeeOyN6kkM5rVNes8F1/HJRbvgRe', 'moi_truong', '0983654335', 'giang_vien'),
(15, '9344477882', 'NGUYỄN THỊ HỒNG HẠNH', 'nthhanh.qldd@hunre.edu.vn', '$2y$10$ewK4JQNityHJvcQjLsQuXuT0q1fcK65ftyIefs6fi/PeBKKUdWIni', 'quan_ly_dat_dai', '0987654331', 'giang_vien'),
(16, '98765435242', 'Trần Văn Tình', 'tvtinh@hunre.edu.vn', '$2y$10$tZhDTAprYdfm5Q1P2g18uOTGeqs1Gw7mksExxMBc/UBEHKXn1nBLa', 'khi_tuong_thuy_van', '0935435543', 'giang_vien'),
(17, '9876654322', 'Nguyễn Văn Nam', 'nvnam.tdbdv@hunre.edu.vn', '$2y$10$CCfqP56lMDQSBzimSV8nROA48xWUlKT9j9SXOg4z1whcrLsdcXY.a', 'trac_dia_ban_do', '0923456781', 'giang_vien'),
(18, '2345678876', 'Phí Trường Thành', 'ptthanhdc@hunre.edu.vn', '$2y$10$l32cAL5BMroYRJ.eA0XVFOKCqMtaqwgBcCFYUNxgU3RG.f5NdV8aW', 'dia_chat', '0951235213', 'giang_vien'),
(19, '2345546778', 'TRẦN THÙY CHI', 'ttchi@hunre.edu.vn', '$2y$10$G86guQ2pnFdkr9X83gkcj.3vvc/HJw1o3FJM05D./ACpYRZgcQp16', 'tai_nguyen_nuoc', '0378122444', 'giang_vien'),
(20, '3692617003', 'Đỗ Minh Anh', 'dmanh@hunre.eu.vn', '$2y$10$upMRDAN/yJPCw6TsJZYlN.UUK7EELC7HP5AWZDlHy6VowqZTKr7Pi', 'ly_luan_chinh_tri', '0956213333', 'giang_vien'),
(21, '3456778993', 'Vũ Văn Lân', 'vuduclan123@gmail.com', '$2y$10$9a2iLYHZr3M3rUOQlZ36WeHQafa1PNRFgh6FyxDP5Xx6KQgSxey4m', 'bien_hai_dao', '0912312334', 'giang_vien'),
(22, '1233443562', 'Lê Ngọc Anh', 'lnanh@gmail.com', '$2y$10$Gd8TCrIGyDx4XTjeYaZuzuDGyoEoYTo27.3RdqBz.9pxTNpcE9iQ6', 'khoa_hoc_dai_cuong', '0963262333', 'giang_vien'),
(23, '9130851553', 'NGÔ THẾ ANH', 'Ngotheanhnb@gmail.com', '$2y$10$fWfzgjUtaotB7wf8t9zVUuuV..yZRXtTDs5.JH0H7zIoz5kuk70t6', 'bo_mon_luat', '0961233342', 'giang_vien'),
(24, '9035495493', 'NGUYỄN THỊ MỸ VÂN', 'ngmyvan@gmail.com', '$2y$10$p2PUEFwPQcsznde/6Xyjz.7hOHkjnxeR/thZxM/dfiXBXUNHVUJ5K', 'bien_doi_khi_hau', '0963272423', 'giang_vien'),
(25, '8328320005', 'Nguyễn Thị Thanh An', 'nttan@hunre.edu.vn', '$2y$10$W/X0XrkIDzKxoWOxW/yXceyEhNO9o6QfvQpG0.0mP0kT5p6iy3nBa', 'ngoai_ngu', '0962736723', 'giang_vien');

-- --------------------------------------------------------

--
-- Table structure for table `lich_thuc_tap`
--

CREATE TABLE `lich_thuc_tap` (
  `id` int(11) NOT NULL,
  `ma_dang_ky` int(11) NOT NULL,
  `stt_sv` int(11) NOT NULL,
  `ngay_thuc_tap` date NOT NULL,
  `ca_lam` varchar(10) NOT NULL CHECK (`ca_lam` in ('Sáng','Chiều','Tối')),
  `thoi_gian_ca` varchar(20) NOT NULL,
  `ngay_cap_nhat` date NOT NULL,
  `danh_gia` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lich_thuc_tap`
--

INSERT INTO `lich_thuc_tap` (`id`, `ma_dang_ky`, `stt_sv`, `ngay_thuc_tap`, `ca_lam`, `thoi_gian_ca`, `ngay_cap_nhat`, `danh_gia`) VALUES
(5, 20, 64, '2025-03-06', 'Sáng', '8:00 12:00', '2025-06-03', 'Tốt');

-- --------------------------------------------------------

--
-- Table structure for table `pdf_nhan`
--

CREATE TABLE `pdf_nhan` (
  `id` int(11) NOT NULL,
  `stt_danhgia` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `ket_qua` enum('A','B+','B','C','D','F') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pdf_nhan`
--

INSERT INTO `pdf_nhan` (`id`, `stt_danhgia`, `filename`, `filepath`, `created_at`, `ket_qua`) VALUES
(50, 10, 'DanhGiaThucTap_22111061353_1748902703.pdf', 'DanhGiaThucTap_22111061353_1748902703.pdf', '2025-06-03 05:18:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sinh_vien`
--

CREATE TABLE `sinh_vien` (
  `stt_sv` int(11) NOT NULL,
  `ma_sinh_vien` varchar(50) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `lop` varchar(50) DEFAULT NULL,
  `khoa` varchar(50) DEFAULT NULL,
  `so_hieu` varchar(50) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `role` enum('sinh_vien') NOT NULL DEFAULT 'sinh_vien'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sinh_vien`
--

INSERT INTO `sinh_vien` (`stt_sv`, `ma_sinh_vien`, `ho_ten`, `email`, `password`, `lop`, `khoa`, `so_hieu`, `so_dien_thoai`, `role`) VALUES
(1, '22111061351', 'ĐẶNG NAM ANH', '22111061351@hunre.edu.vn', '$2y$10$xdOCClqYuVHlV31wU/T1v.DzQkgJN7mpPJZiTK05/Mc97u9Jz40UG', 'a1', 'kinh_te', NULL, NULL, 'sinh_vien'),
(3, '22111061031', 'NGUYỄN THỊ MINH ANH', '22111061031@hunre.edu.vn', '$2y$10$e7GgoarB0lB0gfqoPV/SzOvStNFrn6BN3wnKJgwrYBtnxfBVDtN9G', 'a2', 'moi_truong', NULL, NULL, 'sinh_vien'),
(4, '22111061314', 'NGUYỄN THỊ VÂN ANH', '22111061314@hunre.edu.vn', '$2y$10$aWNZh.kFvBTjJDSXHzrKYemxljt/x9j6PgCbEFHf2l2w5CaBu7aVK', 'a2', 'moi_truong', NULL, NULL, 'sinh_vien'),
(5, '22111060935', 'TRẦN HẢI ANH', '22111060935@hunre.edu.vn', '$2y$10$0L7jVLR6NzC.NjBhVqy1lekWFnN/4I21YDyN3AosUjEgkUrqqkYYC', 'quanlydatdai', 'quan_ly_dat_dai', NULL, NULL, 'sinh_vien'),
(6, '22111060967', 'LƯƠNG QUYẾT CHIẾN', '22111060967@hunre.edu.vn', '$2y$10$qHDxGUkw6aUS5u9RdeFv8eakmZViWCntC6kwH5.VdYQZwRoSDEDMW', 'quanlydatdai', 'quan_ly_dat_dai', NULL, NULL, 'sinh_vien'),
(64, '1234567890', 'TRẦN THI NHƯ HOA', 'trannhuhoa28@gmail.com', '$2y$10$YVc62G2GxxEeMNorYq.rce9nr6SH4ECmcb8L9aCTa9vM9RvI0TKhe', 'a3', 'bo_mon_luat', '9130851553', '1234567890', 'sinh_vien'),
(67, '1234567892', 'Vũ Thảo Vân', 'vuthaovan088@gmail.com', '$2y$10$1.JAfPTQxcEWZqvdZYPKpeVWIpp1f7IbDFgnsSk9/6azL97cfI7LC', 'a3', 'bo_mon_luat', '9130851553', '1234267890', 'sinh_vien'),
(74, '22111061302', 'Mguyễn Thị Diệu', '22111061302@hunre.edu.vn', '$2y$10$mJXK69uMclvV3ON2ot8rjeVScTR9BhuPVQ2l74ZiNHjLLHVsBzbhy', 'kinhte', 'kinh_te', '2438370598', NULL, 'sinh_vien'),
(75, '22111061237', 'Trần Nọc Dương', '22111061237@hunre.edu.vn', '$2y$10$COfhhv/qSvN3auEi76DkyOpgvKIcZJf0dsdjEeiVlre1i0Y/8u94u', 'kinhte', 'kinh_te', '2438370598', NULL, 'sinh_vien'),
(76, '22111061424', 'Đại Quốc Đạt', '22111061424@hunre.edu.vn', '$2y$10$IJwDuvLsxtZvuYcjiB80SONPmk.xL9HGRMiKQ.JlX5/tDsowTGtLe', 'kinhte', 'kinh_te', '2438370598', NULL, 'sinh_vien'),
(77, '22111061353', 'Phạm Tiến Đạt', '22111061353@hunre.edu.vn', '$2y$10$uUP67YRVNPH7I.c3rocqDuWx.yxMzm4mnRX3d74qVmMX04tfWsH8O', 'moitruong', 'moi_truong', '9120403251', '0334567890', 'sinh_vien'),
(78, '22111061403', 'Lê Trường Giang', '22111061403@hunre.edu.vn', '$2y$10$3ovifrKGJTaLPDa2AmBGnuReKrek/OwQvEBTv6Ok0wO0mwkUWt7L6', 'moitruong', 'moi_truong', '9120403251', NULL, 'sinh_vien'),
(79, '21111065119', 'Đỗ Hoàng Hà', '21111065119@hunre.edu.vn', '$2y$10$IqoFaNBzeoRI8e0nr6SPfOQ3wuu88Ohk2xEwHCnekkL9EwREkysJS', 'moitruong', 'moi_truong', '9120403251', NULL, 'sinh_vien'),
(80, '22111061026', 'Hoàng Văn Hà', '22111061026@hunre.edu.vn', '$2y$10$c/A4XRkP9Y7g8hgQECIocOldeXg758rq2ofzv7FdvVB/Z4YxNB3Rm', 'quanlydatdai', 'quan_ly_dat_dai', NULL, NULL, 'sinh_vien'),
(81, '22111061137', 'Nguyễn Đức Hà', '22111061137@hunre.edu.vn', '$2y$10$tlRVYLrnm9zpbHMl7mwwUu8amixJG45sCz4XqkB3wUawOWzdxC3Gq', 'quanlydatdai', 'quan_ly_dat_dai', NULL, '0932455681', 'sinh_vien'),
(82, '22111061325', 'Phạm Minh Hà', '22111061325@hunre.edu.vn', '$2y$10$q3SGMG3X/7FUT6nJvAYIZuPrsgkmRAaJj6tv8GWb5f0mALjw5zDV6', 'quanlydatdai', 'quan_ly_dat_dai', NULL, '0937212445', 'sinh_vien'),
(83, '22111061312', 'Hoàng Nghĩa Hải', '22111061312@hunre.edu.vn', '$2y$10$4jZxvwmL9.CFBgGRWBvXK.JvGfeuA7nmtYlR499mH4HduWNsBNttW', 'khituongthuyvan', 'khi_tuong_thuy_van', NULL, '0937358468', 'sinh_vien'),
(84, '22111061278', 'Trịnh Xuân Hải', '22111061278@hunre.edu.vn', '$2y$10$XyF9UkpAPoBIZV0OIWVhlOApfMhDEJ96/OSrHrJWhDZR0oKcAGArS', 'khituongthuyvan', 'khi_tuong_thuy_van', NULL, '0966653487', 'sinh_vien'),
(85, '22111061243', 'Đoàn Thị Hải', '22111061243@hunre.edu.vn', '$2y$10$2nCFC.VSTxWf5g0xeBMepeQs5xtwkXbrbdeut.7KXUiJ.zPB.rriW', 'khituongthuyvan', 'khi_tuong_thuy_van', NULL, '0984567321', 'sinh_vien'),
(86, '22111061340', 'Trần Thị Hằng', '22111061340@hunre.edu.vn', '$2y$10$fhET8JJfKocuEHwpj.8QUO0zqTfa8szf6VIzrzKwtz3WRiO5QTNsO', 'tracdiabando', 'trac_dia_ban_do', '9876654322', '0987654312', 'sinh_vien'),
(87, '22111061159', 'Nguyễn Công Hiếu', '22111061159@hunre.edu.vn', '$2y$10$KQDmlWw7ExwPu6kc6Ly.DeRG41lOR8IxN1nDP2/1BmLyTIdTRdU6O', 'congnghethongtin', 'cntt', '2223456798', '0945465945', 'sinh_vien'),
(88, '22111061338', 'Nguyễn Thái Học', '22111061338@hunre.edu.vn', '$2y$10$uNauUKbz8qj/pUE1Wl/n1OgqRpAOEZn2LfBLfSAk1dIwzpdVp1yh.', 'diachat', 'dia_chat', NULL, '0912345678', 'sinh_vien'),
(90, '22111061363', 'Vũ Thị Minh Tuyết', '22111061363@hunre.edu.vn', '$2y$10$xOEyLVggyT5k2BXAwYUTlu8A8ZDckXGw6VYgQGBSLkvPrkXuzHuO2', 'congnghethongtin', 'cntt', NULL, '0366015791', 'sinh_vien'),
(93, '22111061425', 'Hà Minh Quang', '22111061425@hunre.edu.vn', '$2y$10$qgbS/.HrLmi7DBKqHmFwT.28garbBei8DXwihpfnlYZR2Z8nSDYU.', 'congnghethongtin', 'cntt', '2223456798', '0985113229', 'sinh_vien'),
(94, '22111061173', 'Chử Trung Huân', '22111061173@hunre.edu.vn', '$2y$10$NkoFIocucADQuBWKVxxZ7upd2D4iMCgsoM/BrSMz.7cfjf0ZXBrJG', 'congnghethongtin', 'cntt', NULL, '0985113776', 'sinh_vien'),
(95, '22111061344', 'Nguyễn Thị Huệ', '22111061344@hunre.edu.vn', '$2y$10$c1PDTGlsTF/U..Tvy6D.n.q/fwCGVlvDdBFEbXHbRmgHy306Z1obq', 'congnghethongtin', 'cntt', NULL, '0945345445', 'sinh_vien'),
(96, '22111061393', 'Phạm Văn Huy', '22111061393@hunre.edu.vn', '$2y$10$tlq2lgbs1SDERL3OCQ867e8pVugcFJf6tzsWnI9xIxfa0kRbUGIQW', 'congnghethongtin', 'cntt', NULL, '0983113223', 'sinh_vien'),
(97, '22111061436', 'Bùi Văn Hưng', '22111061436@hunre.edu.vn', '$2y$10$.Ng4/6tIjxHCnGbMUS0K8OE3HiBmDDjUHC0wfxYoLvDqaZpMudq4m', 'congnghethongtin', 'cntt', NULL, '0983413123', 'sinh_vien'),
(98, '22111060953', 'Nguyễn Thị Ngọc Khánh', '22111060953@hunre.edu.vn', '$2y$10$.h81w4WWrtuXguM5dQEMk.7naZ4h4ns4nEsQ6Yy7QdmOONwYSMyri', 'congnghethongtin', 'cntt', '2223456798', '0945845845', 'sinh_vien'),
(99, '22111061006', 'Nguyễn Sỹ Kiên', '22111061006@hunre.edu.vn', '$2y$10$lWvnE3sQJL.YWY0Iux5EcOsc.anA5sEwThmN6IN8n3n7dORasQvVS', 'congnghethongtin', 'cntt', '2223456798', '0911322244', 'sinh_vien'),
(100, '22111061015', 'Nguyễn Phương Linh', '22111061015@hunre.edu.vn', '$2y$10$kkrypzbgOy0Gs.KCROAjPeQ/QMYwzQfh4neX8j.OEW.ukwj0F92U.', 'congnghethongtin', 'cntt', NULL, '0985229113', 'sinh_vien'),
(101, '22111061308', 'Nguyễn Thị Thùy Linh', '22111061308@hunre.edu.vn', '$2y$10$n2hEr1cwTh09Nw7YyKzjYuPhK17lwaxj8R2nOjKduerXvOSsY3oky', 'congnghethongtin', 'cntt', NULL, '0123456789', 'sinh_vien'),
(102, '2211061199', 'Nguyễn Công Việt', '2211061199@hunre.edu.vn', '$2y$10$q11zBpCT2mhBCDHqGPPU1OuBtBWxoIvEs9qIcCQLJPrh02OdBaj2a', 'the_chat_quoc_phong', 'the_chat_quoc_phong', NULL, '0987654322', 'sinh_vien');

-- --------------------------------------------------------

--
-- Table structure for table `tuyen_dung`
--

CREATE TABLE `tuyen_dung` (
  `stt_tuyendung` int(11) NOT NULL,
  `ma_tuyen_dung` varchar(50) NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `stt_cty` int(11) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `so_luong` int(11) DEFAULT 1 CHECK (`so_luong` > 0),
  `han_nop` date NOT NULL,
  `trang_thai` enum('Đang chờ','Đã duyệt','Bị từ chối') DEFAULT 'Đang chờ',
  `dia_chi` varchar(255) NOT NULL,
  `hinh_thuc` enum('Full-time','Part-time') DEFAULT NULL,
  `gioi_tinh` enum('Nam','Nữ','Không giới hạn') NOT NULL,
  `noi_bat` tinyint(1) DEFAULT 0,
  `khoa` enum('kinh_te','moi_truong','quan_ly_dat_dai','khi_tuong_thuy_van','trac_dia_ban_do','dia_chat','tai_nguyen_nuoc','cntt','ly_luan_chinh_tri','bien_hai_dao','khoa_hoc_dai_cuong','the_chat_quoc_phong','bo_mon_luat','bien_doi_khi_hau','ngoai_ngu') DEFAULT NULL,
  `trinh_do` enum('Không yêu cầu','Trung cấp','Cao đẳng','Đại học','Thạc sĩ','Tiến sĩ') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tuyen_dung`
--

INSERT INTO `tuyen_dung` (`stt_tuyendung`, `ma_tuyen_dung`, `tieu_de`, `stt_cty`, `mo_ta`, `so_luong`, `han_nop`, `trang_thai`, `dia_chi`, `hinh_thuc`, `gioi_tinh`, `noi_bat`, `khoa`, `trinh_do`) VALUES
(30, 'TD17476269602351', 'Nhân Viên Tester (QC/ Tester)', 13, 'Mô tả công việc\\r\\n- Phối hợp với các đội phát triển phần mềm để hiểu rõ về dự án và mục tiêu kiểm thử cũng như các yêu cầu đưa ra. Đọc hiểu tài liệu nghiệp vụ & tài liệu design của sản phẩm.\\r\\n\\r\\n- Thiết kế và xây dựng các trường hợp kiểm thử theo nhiều phương pháp khác nhau\\r\\n\\r\\n- Thực hiện kiểm tra, log lỗi, và theo dõi tiến độ fix bug\\r\\n\\r\\n- Kiểm soát chất lượng, đảm bảo hệ thống/sản phẩm được xây dựng đúng như thiết kế hệ thống và đáp ứng được yêu cầu nghiệp vụ\\r\\n\\r\\n- Phối hợp chặt chẽ với developers, designer & suppoter trong các kế hoạch release', 23, '2025-08-05', 'Đã duyệt', 'Tòa Nhà Kim Ánh, 78 Duy Tân, Cầu Giấy, Hà Nội', 'Part-time', 'Không giới hạn', 1, 'cntt', 'Đại học'),
(31, 'TD17476276377537', 'Mid. Frontend Dev - HTML5/CSS3/JavaScript', 14, 'Mô tả công việc\\r\\nSolis Lab is hiring now Frontend Dev - HTML5/CSS3/JavaScript. Join Solis Lab, a dynamic team crafting websites for top-tier brands with millions of monthly views. We\\\'re looking for a developer who is eager to turn intricate designs into stunning, responsive, and accessible web components. If you\\\'re detail-oriented, proactive, and ready to dive into front-end web development best practices, we want you!\\r\\n\\r\\nYour main responsibilities include:\\r\\n\\r\\nTransform design files (Sketch, Zeplin, Figma) into responsive and accessible front-end components.\\r\\nUphold and advance accessibility, performance, and coding standards.\\r\\nCollaborate with the team leader and project manager to keep projects on schedule and aligned with goals.\\r\\nEngage with the technical lead to research and adopt cutting-edge front-end practices.\\r\\nYou are perfect for this position if:\\r\\n\\r\\n Have a keen eye for details and can interpret design requirements into front-end code precisely.\\r\\nWrites clean, maintainable code that is designed for reusability and scalability across various projects.\\r\\nDemonstrates independence and initiative, proactively researching and devising technical solutions for challenging tasks.\\r\\nConducts thorough quality assurance on your work before seeking reviews from team leaders.\\r\\nSupports fellow team members, is willing to share knowledge, and provides mentorship to less experienced developers.\\r\\n Takes pride in delivering high-quality work and is committed to excellence. Only apply if you\\\'re ready to invest the time and effort required to achieve the highest standards.', 5, '2025-08-20', 'Đã duyệt', 'Tầng 4 - tòa nhà Hà Thành Plaza - 102 Thái Thịnh, Quận Đống Đa, Hà Nội', 'Full-time', 'Không giới hạn', 1, 'cntt', ''),
(32, 'TD17476286528372', 'Senior Cloud Solution Architect (AWS)', 15, 'MÔ TẢ CÔNG VIỆC\\r\\nVị trí: Kiến trúc sư Giải pháp Đám mây (Cloud Solution Architect)\\r\\nĐịa điểm: MEGAZONE Việt Nam\\r\\n\\r\\nMEGAZONE Việt Nam đang tìm kiếm một Kiến trúc sư Giải pháp Đám mây giàu kinh nghiệm, chuyên về các giải pháp điện toán đám mây. Vai trò này đóng vai trò then chốt trong việc cung cấp các thiết kế kỹ thuật và giải pháp sáng tạo cho khách hàng tại Việt Nam, thúc đẩy việc ứng dụng các công nghệ đám mây.\\r\\n\\r\\nLà một thành viên cấp cao trong đội ngũ kỹ thuật, bạn sẽ đóng vai trò quan trọng trong việc định hình và thực thi các chiến lược kỹ thuật trong một môi trường kinh doanh năng động và đang phát triển nhanh chóng.\\r\\n\\r\\nTrách nhiệm chính:\\r\\nChịu trách nhiệm về thành công kỹ thuật của các dự án và tương tác với khách hàng.\\r\\n\\r\\nĐịnh nghĩa kiến trúc triển khai mạnh mẽ và hiệu quả.\\r\\n\\r\\nSoạn thảo các phản hồi kỹ thuật cho các yêu cầu đề xuất (RFPs).\\r\\n\\r\\nPhát triển chuyên môn sâu về các dịch vụ và công nghệ đám mây.\\r\\n\\r\\nHợp tác chặt chẽ với các đội nhóm quốc tế tại MEGAZONE CLOUD và các đối tác bên ngoài.\\r\\n\\r\\nĐóng vai trò là chuyên gia kỹ thuật (Subject Matter Expert) cho khách hàng trong khu vực.\\r\\n\\r\\nTham gia chủ động vào các dự án thử nghiệm (pilot projects) và phát triển các bản trình diễn giải pháp kỹ thuật.\\r\\n\\r\\nBáo cáo trực tiếp cho Trưởng phòng Kinh doanh MEGAZONE Việt Nam.', 3, '2025-11-22', 'Đã duyệt', '54 Lieu Giai, Quận Ba Đình, Hà Nội', 'Full-time', 'Không giới hạn', 1, 'cntt', 'Đại học'),
(33, 'TD17476290331499', 'Nhân Viên Kinh Doanh', 16, 'Mô tả công việc\\r\\nNghe điện thoại khách gọi đến nhờ tư vấn, tìm kiếm mua nhà\\r\\nTìm thông tin nhà có sẵn trên App của Công ty để ra căn nhà phù hợp nhất với nhu cầu của khách,\\r\\nPhối hợp với đồng nghiệp để hoàn thành giao dịch\\r\\nChi tiết công việc được đào tạo, hướng dẫn kỹ càng, có người thạo việc hướng dẫn cho đến khi tự độc lập làm thành thạo', 34, '2025-10-09', 'Đã duyệt', 'Tầng 2 tòa nhà MIPEC Tower, 229 Tây Sơn, Đống Đa, Hà Nội', 'Full-time', 'Không giới hạn', 1, 'kinh_te', 'Đại học'),
(34, 'TD17476297723189', 'Nhân Viên Xử Lý Khiếu Nại', 17, 'Mô tả công việc\\r\\n- Tiếp nhận và xử lý các vấn đề phát sinh sau mua với các đối tác TQ qua sàn Thương mại điện tử.\\r\\n\\r\\n- Liên hệ người bán, người mua, thuyết phục, xử lý và phối hợp với các bộ phận liên quan để hoàn tất yêu cầu khiếu nại.\\r\\n\\r\\n- Theo dõi quá trình khiếu nại với đối tác TQ để xử lý kịp thời, hiệu quả.\\r\\n\\r\\n- Làm các việc khác theo yêu cầu của cấp trên.', 6, '2025-09-30', 'Đã duyệt', '45 Trần Thái Tông, Quận Cầu Giấy, Thành phố Hà Nội', 'Part-time', 'Nữ', 1, 'kinh_te', 'Đại học'),
(35, 'TD17476301692948', 'Kế Toán Thuế', 18, 'Mô tả công việc\\r\\n- Công việc chủ yếu liên quan đến báo cáo hàng quý và báo cáo tài chính, xử lý các công việc phát sinh liên quan đến thuế.\\r\\n\\r\\n- Tập hợp chứng từ thuế\\r\\n\\r\\n- Tư vấn quy định pháp luật về thuế, kế toán\\r\\n\\r\\n- Xử lý các công việc về thuế, kế toán\\r\\n\\r\\n- Triển khai, xử lý, giao tiếp với khách hàng trong công tác dịch vụ thuế, kế toán', 4, '2025-10-15', 'Đã duyệt', 'Tầng 2, 3 nhà số 38 ngách 124/35 Miêu Nha, TDP 1, Phường Tây Mỗ, Quận Nam Từ Liêm, Hà Nội', 'Part-time', 'Không giới hạn', 1, 'kinh_te', 'Đại học'),
(36, 'TD17476304563245', 'Nhân Viên Nghiệp Vụ Soạn Hồ Sơ', 19, 'Mô tả công việc\\r\\n- Nắm được thủ tục hồ sơ xin visa đu lịch, công tác, thăm thân các nước\\r\\n\\r\\n- Nắm được thủ tục soạn hồ sơ giấy phép lao động, thẻ tạm trú cho người nước ngoài làm việc tại Việt Nam.\\r\\n\\r\\n- Cung cấp thông tin tư liệu cho sale tư vấn khách\\r\\n\\r\\n- Soạn, nộp hồ sơ và lấy kết quả .', 5, '2025-10-30', 'Đã duyệt', '21 Ngọc Hà, Phường Ngọc Hà, Quận Ba Đình, Hà Nội', 'Part-time', 'Không giới hạn', 1, 'bo_mon_luat', 'Đại học'),
(37, 'TD17476308698281', 'Assistant General Director', 20, 'Hỗ trợ điều hành\\r\\n\\r\\nTiếp nhận và truyền đạt các chỉ đạo từ Tổng Giám đốc (TGĐ) đến các phòng ban và nhà máy liên quan.\\r\\n\\r\\nPhối hợp với các bộ phận để triển khai các công việc được TGĐ giao.\\r\\n\\r\\nTheo dõi tiến độ và đảm bảo các nhiệm vụ được hoàn thành đúng thời hạn và hiệu quả.\\r\\n\\r\\n2. Quản lý thông tin & Báo cáo\\r\\n\\r\\nTổng hợp báo cáo hàng tuần từ các nhà máy và phòng ban.\\r\\n\\r\\nPhân tích số liệu theo yêu cầu của TGĐ.\\r\\n\\r\\nCập nhật và tóm tắt các quy định pháp luật mới và thông tin liên quan đến ngành.\\r\\n\\r\\nTheo dõi và đảm bảo việc tuân thủ các yêu cầu của khách hàng về tiêu chuẩn quản lý chất lượng và trách nhiệm xã hội.\\r\\n\\r\\nGiám sát và theo dõi các email quan trọng từ khách hàng và đối tác, đảm bảo phản hồi kịp thời.\\r\\n\\r\\n3. Hỗ trợ hành chính & Đối ngoại\\r\\n\\r\\nSoạn thảo, dịch thuật và rà soát các văn bản, hợp đồng, công văn theo chỉ đạo của TGĐ.\\r\\n\\r\\nKiểm tra tính chính xác của tài liệu trước khi trình ký.\\r\\n\\r\\nThuyết trình và hỗ trợ hướng dẫn khi có khách đến thăm công ty.\\r\\n\\r\\nSắp xếp và nhắc lịch làm việc, họp của TGĐ với các đơn vị liên quan.\\r\\n\\r\\nHỗ trợ tổ chức hội thảo, sự kiện của công ty khi cần thiết.\\r\\n\\r\\nChuẩn bị phòng họp và các tài liệu cần thiết.\\r\\n\\r\\n4. Tham gia họp & Các nhiệm vụ khác\\r\\n\\r\\nTham dự các cuộc họp cùng TGĐ, ghi biên bản và tổng hợp báo cáo cuộc họp.\\r\\n\\r\\nThực hiện các công việc khác theo phân công của TGĐ.\\r\\n\\r\\nXây dựng kế hoạch làm việc hàng tuần và nộp báo cáo định kỳ.', 1, '2025-10-16', 'Đã duyệt', 'Tầng 11, tòa nhà Veam Tây Hồ, ngõ 689 đường Lạc Long Quân, Phường Phú Thượng, Quận Tây Hồ, Hà Nội', 'Part-time', 'Không giới hạn', 0, 'ngoai_ngu', 'Đại học'),
(38, 'TD17476311517957', 'Trưởng Phòng QA – Đảm Bảo Chất Lượng (QA Manager)', 21, 'Mô tả công việc\\r\\nQuản lý hệ thống chất lượng theo tiêu chuẩn GMP, GLP, GSP, ISO 9001.\\r\\nGiám sát quy trình sản xuất dược phẩm, kiểm soát chất lượng từ nguyên liệu đến thành phẩm.\\r\\nPhê duyệt xuất xưởng, xử lý khiếu nại, thu hồi sản phẩm không đạt chuẩn.\\r\\nQuản lý tài liệu, hồ sơ chất lượng, đánh giá nhà cung cấp.\\r\\nĐào tạo, phát triển đội ngũ QA; xây dựng KPIs, tiêu chuẩn công việc.\\r\\nTham mưu cho Ban Giám đốc về chiến lược chất lượng và cải tiến hệ thống.', 1, '2025-10-29', 'Đã duyệt', 'Tầng 2, tòa G1, Vinhomes Greenbay, Mễ Trì, Hà Nội', 'Part-time', 'Không giới hạn', 0, 'moi_truong', 'Đại học'),
(43, 'TD17478449812802', 'Kỹ Sư Dự Án Môi Trường', 25, 'Giám sát việc thi công, lắp đặt thiết bị, vận hành tại công trường;\r\nGiải quyết vướng mắc trong quá trình thi công;\r\nChuẩn bị và triển khai công tác nghiệm thu\r\nThực hiện các nhiệm vụ khác theo chỉ đạo của cấp trên\r\nLàm việc tại các dự án xung quanh Hà Nội hoặc các tỉnh miền bắc', 5, '2025-09-25', 'Đã duyệt', 'Tầng 5, Tòa nhà Udic Complex, Đường Hoàng Đạo Thúy, P. Trung Hòa, Q. Cầu Giấy, Hà Nội', 'Full-time', 'Không giới hạn', 1, 'moi_truong', 'Đại học'),
(48, 'TD17478481178063', 'Report Technician – Geotechnical Investigation/ Kỹ thuật viên Báo cáo – Khảo sát Địa chất', 27, 'Manage input of GI data for UK offices.\r\nDrillers records\r\nSupport with automation of processes, where suitable\r\nGeneration of factual report content, including collation of data, preparation of the text, processing of photographs.\r\nImport of digital data (AGS) into the central database with data checking and correction\r\nEngineers logging of borehole samples, trial pits\r\nField test data such as dynamic cone penetrometers, gas and groundwater monitoring, permeability testing\r\nData process engineer logs through Openground database system\r\nData process field testing through Openground database system\r\nLiaise with project managers/Operations and Principal Geologist to prioritise and organise workload.\r\nAssist with production of Draft / Final reports\r\nResponsible for mounting site photographs / core photographs on to report templates.\r\nAssist in production of paper reports\r\nEnsure client reporting deadlines are met', 5, '2025-08-13', 'Đã duyệt', '17 Ng. 575 P. Kim Mã, Ngọc Khánh, Ba Đình, Hà Nội', 'Full-time', 'Không giới hạn', 1, 'dia_chat', 'Đại học'),
(49, 'TD17478500607348', 'Chuyên Viên Đầu Tư Phát Triển Dự Án', 32, 'Thực hiên công tác chuẩn bị đầu tư dự án: Thủ tục chấp thuận chủ trương đầu tư, thủ tục lập quy hoạch, thủ tục lập báo cáo nghiên cứu khả thi, thiết kế xây dựng, thủ tục đất đai, giải phóng mặt bằng, các thủ tục xin giao đất…\r\n\r\n- Thiết lập, xây dựng, duy trì và phát triển mối quan hệ với các cơ quan quản lý Nhà nước, với cơ quan chính quyền địa phương trong việc xin cấp phép các thủ tục pháp lý liên quan đến đầu tư dự án.\r\n\r\n- Theo dõi các công việc phát sinh liên quan đến thủ tục pháp lý của các dự án.\r\n\r\n- Hỗ trợ cung cấp thông tin về Dự án cho các bộ phận khác đảm bảo tính chính xác, kịp thời', 5, '2025-09-25', 'Đã duyệt', 'Số 349 phố Vũ Tông Phan, Phường Khương Đình, Quận Thanh Xuân, Thành phố Hà Nội', 'Full-time', 'Không giới hạn', 1, 'quan_ly_dat_dai', 'Đại học'),
(50, 'TD17478502613702', 'Chuyên Viên Giải Phóng Mặt Bằng', 33, '1. Lập hồ sơ, Quản lý hồ sơ, theo dõi tiến độ giải phóng mặt bằng (GPMB) đất thuộc dự án;\r\n\r\n2. Tham gia, phối hợp với cơ quan địa phương có thẩm quyền để thực hiện công việc về GPMB. Đồng thời là đầu mối làm việc các cơ quan chức năng và chính quyển địa phương về công tác GPMB;\r\n\r\n3. Tổng hợp các khó khăn, vướng mắc trong quá trình thực hiện; ý kiến, nguyện vọng, đề nghị của cá nhân, tổ chức bị thu hồi đất báo cáo, đề xuất ban Giám đốc xem xét giải quyết;\r\n\r\n4. Hướng dẫn, giải đáp các thắc mắc của cá nhân, tổ chức về những vấn đề liên quan đến GPMB;\r\n\r\n5. Tham gia các cuộc họp, đàm phán thực hiện công tác GPMB;\r\n\r\n6. Quản lý mặt bằng đất đã nhận bàn giao;\r\n\r\n7. Tham mưu tổ chức thi công GPMB;\r\n\r\n8. Kết hợp với Phòng/ban công ty trong việc hoàn thiện hồ sơ, thủ tục để thực hiện nhiệm vụ GPMB đất dự án;\r\n\r\n9. Lưu trữ hồ sơ giải phóng mặt bằng, hỗ trợ, bồi thường của Dự án theo quy định;', 5, '2025-10-21', 'Đã duyệt', 'Tòa nhà XPhome Star - Khu đô thị Tân Tây Đô - Đan Phượng - Hà Nội', 'Full-time', 'Không giới hạn', 0, 'quan_ly_dat_dai', 'Đại học'),
(51, 'TD17478505055176', 'Chuyên viên QLTK Cấp thoát nướ', 34, 'Nghiên cứu các số liệu đầu vào: quy hoạch chung, các số liệu khảo sát thủy lực, thủy văn, địa hình, các số liệu dự báo…để phân tích, đánh giá và lựa chọn các giải pháp thiết kế, phương án kết nối hạ tầng kỹ thuật trong và ngoài công trình, giải pháp tổ chức mạng lưới hạ tầng kỹ thuật, giải pháp chữa cháy, giải pháp giảm thiểu tác động môi trường,…;\r\nĐưa ra các nhận định về lợi thế của địa điểm đối với dự án; phân tích mức độ ảnh hưởng của các dự án lân cận đối với dự án đang được đề xuất, các rủi ro về kỹ thuật, công nghệ; Yêu cầu đầu vào và đầu ra của dự án;\r\nLập và/hoặc kiểm tra nhiệm vụ thiết kế, các khảo sát, thí nghiệm yêu cầu phục vụ cho thiết kế Cấp thoát nước, Chữa cháy;\r\nQuản lý, kiểm soát hồ sơ thiết kế thuộc chuyên ngành Cấp thoát nước, Chữa cháy đảm bảo sự tuân thủ vả phù hợp với các yêu cầu trong Quy chuẩn, Tiêu chuẩn và mục tiêu của dự án;\r\nKiểm tra, rà soát công tác lập hợp đồng, các công tác liên quan đến nghiệm thu, thanh toán các sản phẩm thiết kế theo chuyên môn và phân quyền;\r\nCung cấp bản vẽ, hồ sơ giải trình kỹ thuật, văn bản, tờ trình phục vụ các công tác thẩm định hồ sơ thiết kế và cấp phép xây dựng: Công tác xin phép đấu nối hạ tầng, xin thẩm duyệt PCCC, giải trình khác…và/hoặc khi có yêu cầu từ lãnh đạo Ban;\r\nNghiên cứu, nắm vững và cập nhật các quy chuẩn, tiêu chuẩn liên quan đến chuyên môn;\r\nNghiên cứu các thành tựu về khoa học công nghệ liên quan đến chuyên ngành Cấp thoát nước, Chữa cháy để ứng dụng vào các Dự án trong hệ thống VPI;\r\nKiểm soát tính tuân thủ các yêu cầu về thiết kế, kỹ thuật, công nghệ liên quan đến chuyên ngành Cấp thoát nước, Chữa cháy đối với toàn bộ các quá trình triển khai đầu tư xây dựng dự án;\r\nCác công việc khác theo sự phân công của cấp quản lý.', 5, '2025-07-24', 'Đã duyệt', 'Tòa nhà Văn Phú - Số 104 Thái Thịnh - Trung Liệt - Đống Đa - Hà Nội', 'Full-time', 'Không giới hạn', 1, 'khi_tuong_thuy_van', 'Đại học'),
(52, 'TD17478509279307', 'Kỹ Sư Thủy Lợi', 35, 'Công việc cụ thể trao đổi khi phỏng vấn\r\n- Quản lý, giám sát, theo dõi thi công đối với các dự án giao thông thủy lợi, hạ tầng kỹ thuật, xây dựng dân dụng và công nghiệp do công ty thực hiện, đảm bảo kỹ thuật chất lượng, hiệu quả kinh tế,tiến độ.', 4, '2025-06-12', 'Đã duyệt', 'Cầu Giấy, Hà Nội', 'Full-time', 'Không giới hạn', 0, 'khi_tuong_thuy_van', 'Đại học'),
(53, 'TD17478513254191', 'Kỹ sư trắc địa', 36, 'MÔ TẢ:\r\n\r\n- Kiểm soát và triển khai công việc về tim, tuyến, cao độ theo đúng Hồ sơ thiết kế.\r\n\r\n- Triển khai công việc hàng ngày để các tổ đội thi công.\r\n\r\n- Thực hiện các công việc khác theo sự phân công của Trưởng bộ phận và Thủ trưởng đơn vị', 10, '2025-06-20', 'Đã duyệt', 'Số 201 Minh Khai, phường Minh Khai, quận Hai Bà Trưng, Hà Nội', 'Full-time', 'Không giới hạn', 0, 'trac_dia_ban_do', 'Đại học'),
(54, 'TD17478520534056', 'Chuyên Viên Kiểm Định', 37, '1. Thực hiện kiểm định theo kế hoạch và sự phân công.\r\n\r\n- Kiểm tra chất lượng Vàng, Bạc, Platium và các kim loại khác\r\n\r\n- Quản lý và chịu trách nhiệm kiểm tra các thiết bị kiểm định đạt chính xác mỗi ngày.\r\n\r\n- Thực hiện thao tác chuẩn hóa trước khi sử dụng thiết bị theo quy định.\r\n\r\n- Tiếp nhận mẫu vật cần kiểm định (Đo), nhận dạng phần mềm tương ứng và thực hiện kiểm định theo phạm vi, đối tượng được giao.\r\n\r\n- Thực hiện các bước chuẩn bị vật Đo đúng quy định để có kết quả Đo chính xác nhất.\r\n\r\n- Thu thập kết quả Đo và tiến hành thủ tục khai báo kết quả theo quy trình, quy định.\r\n\r\n- Báo cáo ngay khi phát hiện chất lạ trong vật Đo.\r\n\r\n- Chịu trách nhiệm về kết quả Đo trước lãnh đạo và khách hàng.\r\n\r\n- Bảo quản vật Đo, nhận dạng và niêm phong vật Đo đúng quy định.\r\n\r\n- Báo cáo với giám sát khi phát hiện sự bất thường trong kiểm định nguyên liệu để xử lý kịp thời, phù hợp.\r\n\r\n- Ghi nhật ký kiểm định, lưu kết quả kiểm định của từng mẫu kiểm.\r\n\r\n- Báo cáo kết quả làm việc mỗi ngày và định kỳ cho giám sát.\r\n\r\n- Phối hợp với các đơn vị liên quan để xử lý các sự cố phát sinh trong quá trình kiểm định.\r\n\r\n2. Kiểm tra, hiệu chỉnh thiết bị Đo lường và phần mềm đo lường luôn luôn đạt chuẩn kiểm định. An toàn, chính xác khi sử dụng.\r\n\r\n- Thực hiện thao tác chuẩn cho thiết bị trước khi Đo.\r\n\r\n- Bảo quản các phương tiện Đo và các mẫu chuẩn sử dụng cho việc cân chỉnh, hiệu chuẩn, bảo hành (mẫu chuẩn, dụng cụ chuẩn, tem kiểm định).\r\n\r\n- Chịu trách nhiệm về những sai sót về thiết bị và phần mềm khi được phân công quản lý.\r\n\r\n- Báo cáo với giám sát khi phát hiện sự bất thường trong khi bảo trì, hiệu chuẩn và kiểm định các phương tiện đo kiểm cho các khu vực sản xuất.\r\n\r\n- Ghi nhật ký kiểm định, lưu kết quả bảo trì, hiệu chuẩn và kiểm định các phương tiện đo kiểm.\r\n\r\n- Báo cáo kết quả làm việc định kỳ cho giám sát. Đề xuất các cải tiến trong hoạt động Đo.\r\n\r\n- Hiệu chuẩn thiết bị kiểm định tại công ty.\r\n\r\n3. Thực hiện các nội dung công việc liên quan đến nội quy lao động, vệ sinh 5S , an toàn.\r\n\r\n- Chuẩn bị phương tiện làm việc, trang bị an toàn, đồng phục lao động.\r\n\r\n- Tham gia và duy trì tình trạng vệ sinh, ngăn nắp tại khu vực làm việc trước, trong và sau giờ làm việc.', 10, '2025-08-14', 'Đã duyệt', 'Số 30 Trần Nhân Tông , Phường Nguyễn Du, Quận Hai Bà Trưng, Hà Nội.', 'Full-time', 'Không giới hạn', 0, 'dia_chat', 'Đại học'),
(55, 'TD17478522976428', 'KỸ SƯ ESG', 38, '1. Nghiên cứu & Tham mưu chiến lược ESG\r\n\r\n- Nghiên cứu xu hướng ESG toàn cầu, đặc biệt trong lĩnh vực bất động sản nghỉ dưỡng và wellness, đề xuất giải pháp phù hợp với định hướng công ty.\r\n\r\n- Phân tích tác động môi trường - xã hội của dự án, đánh giá rủi ro và cơ hội phát triển bền vững.\r\n\r\n- Tham mưu cho CEO về lộ trình triển khai ESG (ngắn hạn/dài hạn), tích hợp vào báo cáo nhà đầu tư.\r\n\r\n2. Quản lý đối tác triển khai ESG\r\n\r\n- Tìm kiếm, lựa chọn và phối hợp với các đối tác tư vấn/công nghệ để triển khai:\r\n\r\n- Hệ thống năng lượng tái tạo (pin mặt trời, biogas).\r\n\r\n- Giải pháp công trình xanh (LEED, WELL, EDGE).\r\n\r\n- Công nghệ quản lý tài nguyên (nước, chất thải, không khí).\r\n\r\n- Giám sát tiến độ và chất lượng triển khai từ đối tác, đảm bảo đúng cam kết ESG.\r\n\r\n3. Xây dựng & Báo cáo ESG\r\n\r\n- Phối hợp với bộ phận Tài chính xây dựng báo cáo ESG theo chuẩn quốc tế (GRI, SASB, TCFD).\r\n\r\n- Theo dõi và đo lường chỉ số hiệu suất ESG (giảm phát thải, tiết kiệm năng lượng, tác động cộng đồng).\r\n\r\n- Hỗ trợ kêu gọi vốn từ quỹ đầu tư xanh (Green Bond, ESG Fund).\r\n\r\n4. Truyền thông nội bộ & Đào tạo\r\n\r\n- Tổ chức đào tạo nhân viên về các tiêu chuẩn ESG và wellness.\r\n\r\n- Cập nhật chính sách pháp lý mới liên quan đến phát triển bền vững.', 5, '2025-07-23', 'Đã duyệt', 'Toà nhà TID, số 4 Liễu Giai, Ba Đình, Hà Nội', 'Full-time', 'Không giới hạn', 0, 'tai_nguyen_nuoc', 'Đại học'),
(56, 'TD17478525218671', 'Kỹ Sư Tài Nguyên Nước', 39, 'Thực hiện công tác điều tra, đánh giá, giám sát chất lượng và bảo vệ tài nguyên nước.\r\n\r\n- Xử lý, cải tạo, phục hồi và phát triển nguồn nước.\r\n\r\n- Điều tra, đánh giá tình hình ô nhiễm, suy thoái, cạn kiệt, nhiễm mặn các nguồn nước theo mức độ suy thoái, ô nhiễm, cạn kiệt.\r\n\r\n- Điều tra, đánh giá các loại hình tác hại do nước gây ra.\r\n\r\n- Lập và xây dựng hành lang bảo vệ các nguồn nước.\r\n\r\n- Lập, quy hoạch và điều chỉnh quy hoạch tài nguyên nước.\r\n\r\n- Công việc cụ thể trao đổi khi phỏng vấn.', 5, '2025-08-13', 'Đã duyệt', 'Số 93, ngõ 95 đường Vũ Xuân Thiều - Phường Sài Đồng - Quận Long Biên - Hà Nội.', 'Full-time', 'Không giới hạn', 0, 'tai_nguyen_nuoc', 'Đại học'),
(57, 'TD17478527738344', 'DevOps Engineer - Khối Công nghệ thông tin (HO25.129)', 40, 'Xây dựng và tối ưu hóa quy trình DevOps/DevSecOps, áp dụng nguyên tắc tự động hóa trong việc triển khai các ứng dụng công nghệ thông tin.\r\nTriển khai, vận hành quản trị các giải pháp/công cụ tự động hóa CICD/IaC, công nghệ Containerization/Kubernetes. (Gitlab, Jenkins, Nexus, Docker, ISTIO, Ansible, Rancher, ...)\r\nTham gia vào giai đoạn phát triển sản phẩm, thiết kế các thành phần phi chức năng đảm bảo an ninh và support trong quá trinh vận hành (thiết kế hệ thống theo các chính sách, quy định đảm bảo an ninh, chuẩn hóa logging, tracing và thông tin monitoring ELK, Prometheus, Grafana, ...)\r\nPhụ trách deploy, optimizing, monitoring, analysis sản phẩm dịch vụ.\r\nThực hiện các nhiệm vụ khác theo yêu cầu của tổ chức.', 10, '2025-07-16', 'Đã duyệt', 'MB Tower, 18 Lê Văn Lương, Trung Hòa, Cầu Giấy, Hà Nội', 'Full-time', 'Không giới hạn', 0, 'cntt', 'Đại học'),
(58, 'TD17478529631704', 'Chuyên gia hạ tầng & hỗ trợ công nghệ thông tin', 41, 'Tổng quan về vị trí:\r\n\r\nChúng tôi đang tìm kiếm một nhân sự có kỹ năng chuyên môn cao để tham gia vào vận hành, đảm bảo hệ thống công nghệ thông tin cho chuỗi sản xuất và bán lẻ hàng thời trang và gia dụng, với hàng trăm cửa hàng trên toàn quốc.\r\n\r\nỨng viên sẽ chịu trách nhiệm xây dựng và duy trì cơ sở hạ tầng hoạt động ổn định, kiểm soát rủi ro để hỗ trợ nền tảng công nghệ bao gồm Python, Postgres, Redis, RabbitMQ, VMWare, NodeJS, MongoDB, MySQL và Linux.\r\n\r\nNhiệm vụ chính:\r\n\r\n1. Quản lý Cơ sở hạ tầng:\r\n\r\nThiết kế, triển khai và duy trì cơ sở hạ tầng có tính sẵn sàng cao và khả năng mở rộng bằng cách sử dụng VMWare cho ảo hóa.\r\nTối ưu hóa và quản lý cơ sở dữ liệu sử dụng Postgres, MongoDB đảm bảo hiệu suất cao và độ tin cậy.\r\nCấu hình và duy trì các dịch vụ middleware như Redis và RabbitMQ để hỗ trợ nhu cầu ứng dụng.Giám sát Hệ thống và Ứng phó Sự cố:\r\nPhát triển và triển khai các chiến lược giám sát cho tất cả các lớp của nền tảng công nghệ, bao gồm hiệu suất của ứng dụng, tình trạng hệ thống và sự ổn định về hạ tầng mạng.\r\n2. Giám sát Hệ thống và Ứng phó Sự cố:\r\n\r\nPhát triển và triển khai các chiến lược giám sát cho tất cả các lớp của nền tảng công nghệ, bao gồm hiệu suất của ứng dụng, tình trạng hệ thống và sự ổn định về hạ tầng mạng.\r\nChủ động xác định các vấn đề tiềm ẩn và giải quyết sự cố để giảm thiểu thời gian ngừng hoạt động và đảm bảo tính sẵn sàng 24/7.\r\nTriển khai các quy trình cảnh báo tự động và ứng phó sự cố để giải quyết các vấn đề một cách nhanh chóng và hiệu quả.\r\n3. Điều chỉnh và Tối ưu hóa Hiệu suất:\r\n\r\nLiên tục đánh giá và tối ưu hóa hiệu suất hệ thống, tập trung vào việc giảm độ trễ và tăng khả năng xử lý.\r\nHợp tác với các nhóm phát triển để tối ưu hóa các ứng dụng Python, NodeJS về hiệu suất và khả năng mở rộng.\r\nTinh chỉnh cơ sở dữ liệu Postgres, MongoDB, MySQL để xử lý hiệu quả các khối lượng truy vấn lớn và phức tạp.\r\n4. Tự động hóa và Công cụ giám sát\r\n\r\nPhát triển và duy trì các công cụ tự động hóa để cải thiện hiệu quả hoạt động, bao gồm các quy trình triển khai, quản lý cấu hình và các công cụ giám sát.\r\nTriển khai áp dụng Infrastructure as Code (IaC) để đảm bảo tính nhất quán và khả năng tái sử dụng trong quá trình triển khai.\r\n5. Hợp tác và Giao tiếp:\r\n\r\nLàm việc chặt chẽ với các nhóm phát triển, vận hành và sản phẩm để đảm bảo việc triển khai và vận hành trơn tru các ứng dụng, đặc biệt là Odoo.\r\nTham gia vào việc đánh giá mã nguồn, thảo luận về kiến trúc và cung cấp các hướng dẫn về độ tin cậy và khả năng mở rộng của hệ thống cho đội ngũ phát triển.\r\n6. Bảo mật và Tuân thủ:\r\n\r\nTriển khai và duy trì các thực tiễn tốt nhất về bảo mật trên toàn bộ cơ sở hạ tầng, đảm bảo tuân thủ các tiêu chuẩn và quy định của ngành.\r\nThường xuyên thực hiện các đánh giá và kiểm tra bảo mật, và giải quyết các lỗ hổng một cách kịp thời.\r\n7. Lập kế hoạch Năng lực và Phục hồi sau Thảm họa:\r\n\r\nTiến hành lập kế hoạch năng lực để đảm bảo cơ sở hạ tầng có thể đáp ứng các nhu cầu hiện tại và tương lai.\r\nPhát triển và duy trì các kế hoạch phục hồi sau thảm họa để đảm bảo tính liên tục của hoạt động kinh doanh trong trường hợp xảy ra các sự cố lớn.', 10, '2025-07-10', 'Đã duyệt', 'Tháp B, tòa Fafim, số 19 Nguyễn Trãi, Thanh Xuân, HN', 'Full-time', 'Không giới hạn', 0, 'cntt', 'Đại học'),
(59, 'TD17478531671651', 'Trưởng phòng công nghệ thông tin', 42, 'Quản lý, vận hành và tối ưu toàn bộ hệ thống hạ tầng CNTT của khách sạn/resort, bao gồm máy chủ, mạng LAN/WAN, hệ thống camera, tổng đài, wifi, và các thiết bị CNTT khác.\r\nLập kế hoạch, triển khai và giám sát các dự án công nghệ – đặc biệt là các phần mềm quản lý khách sạn như PMS (VinHMS,Opera, Smile, eZee, v.v.), POS, CRM, Channel Manager, Booking Engine, và các hệ thống tích hợp khác.\r\nĐảm bảo an toàn thông tin, dữ liệu và hệ thống hoạt động liên tục, không gián đoạn.\r\nQuản lý, đào tạo và phát triển đội ngũ nhân viên CNTT; phân công công việc, đánh giá hiệu quả làm việc định kỳ.\r\nLàm việc với các nhà cung cấp dịch vụ công nghệ, tư vấn giải pháp phù hợp với định hướng phát triển của khách sạn/resort.\r\nPhối hợp với các bộ phận khác để đưa ra giải pháp công nghệ nhằm tối ưu hóa vận hành và nâng cao trải nghiệm khách hàng.\r\nLập ngân sách bộ phận CNTT hàng năm, theo dõi chi phí và kiểm soát theo ngân sách được duyệt.\r\nTham gia xây dựng chiến lược chuyển đổi số và cải tiến công nghệ dài hạn.', 15, '2025-07-31', 'Đã duyệt', 'Số 2 Ngõ 95 Chùa bộc, Đống đa, Hà Nội', 'Full-time', 'Không giới hạn', 0, 'cntt', 'Đại học'),
(60, 'TD17478543576034', 'Giảng Viên Lý Luận Chính Trị', 43, 'Giảng dạy các môn: Triết học Mác Lênin, Tư tưởng Hồ Chí Minh, Đường lối cách mạng Hồ Chí Minh, Kinh tế Chính trị\r\nCác công việc khác theo sự phân công của cán bộ quản lý trực tiếp.', 1, '2025-05-31', 'Đã duyệt', 'Trường Đại học FPT, Khu công nghệ cao Hòa Lạc, Thạch Hòa, Thạch Thất, Hà Nội', 'Part-time', 'Không giới hạn', 0, 'ly_luan_chinh_tri', 'Đại học'),
(61, 'TD17478548834458', 'Trợ Lý Khoa Đại Cương', 44, 'Đầu mối phối hợp triển khai các hoạt động đào tạo với GVCH, GVTG, sinh viên; hỗ trợ các GVCH, GVTG trong việc sử dụng các hệ thống của nhà trường (LMS, IU và E-learning);\r\nThực hiện công tác thư ký cho các cuộc họp Khoa, dự thảo các báo cáo định kỳ và đột xuất của Khoa;\r\nĐầu mối phối hợp với các đơn vị trong và ngoài trường để thực hiện các nhiệm vụ liên quan đến khoa, bao gồm: chuẩn bị/dự thảo hợp đồng, nghiệm thu, thanh lý và xử lý các công việc liên quan với các đơn vị thuê ngoài (outsourcing);\r\nĐầu mối thực hiện công việc hành chính của khoa: công tác văn thư lưu trữ, sắp xếp, bảo quản hồ sơ tài liệu của Khoa; sắp xếp, mua sắm trang thiết bị phục vụ hoạt động của Khoa;\r\nHỗ trợ Lãnh đạo Khoa xây dựng và hệ thống hoá các cơ sở dữ liệu về người học, đối tác, giảng viên, chuyên gia, ngân hàng ý tưởng;\r\nTham gia quản lý nội dung thông tin của Khoa trên các trang truyền thông của Khoa (Email, Facebook, Website, Linkedin,...);\r\nThực hiện các nhiệm vụ khác được phân công.', 5, '2025-07-24', 'Đã duyệt', 'Tây Mỗ - Phường Tây Mỗ - Quận Nam Từ Liêm - Hà Nội.', 'Full-time', 'Không giới hạn', 0, 'khoa_hoc_dai_cuong', 'Đại học'),
(62, 'TD17478551178162', 'CVCC Tư Vấn Pháp Luật - Hà Nội - TA112', 45, '1. Tư vấn pháp lý về các giao dịch có liên quan tới tệp khách hàng mà mình phụ trách bao gồm\r\nnhưng không giới hạn: giao dịch tín dụng, giao dịch bảo bảm, chuyển tiền, ngoại hối, tài\r\nkhoản, etc. đảm bảo giao dịch của khách hàng phù hợp với quy định của pháp luật và quy định\r\nnội bộ của VPBank ban hành từng thời kỳ\r\n2. Kiểm soát và cho ý kiến pháp lý đối với hợp đồng, tài liệu, văn bản … được ký kết giữa\r\nVPBank và Khách hàng bao gồm nhưng không giới hạn hợp đồng tín dụng, hợp đồng bảo\r\nđảm, hợp đồng tiền gửi, hợp đồng mở và sử dụng tài khoản, etc.\r\n3. Kiểm soát tuân thủ pháp lý bao gồm\r\na. Cập nhật văn bản pháp lý mới được ban hành tới các đơn vị mà mình phụ trách. Đánh giá\r\ncác điểm mới, điểm có ảnh hưởng tới hoạt động của đơn vị, đào tạo cho đơn vị về văn bản\r\npháp luật mới (nếu cần)\r\nb. Lập kế hoạch tuân thủ pháp lý theo văn bản pháp luật mới (nếu cần).\r\nc. Kiểm soát tuân thủ pháp lý đối với văn bản nội bộ, sản phẩm theo phân công của cấp quản\r\nlý.\r\nd. Lập báo cáo tuân thủ.\r\n4. Tham gia thực hiện đào tạo pháp lý cho đơn vị nhằm nâng cao am hiểu pháp lý cho đơn vị\r\nmà mình phụ trách\r\n5. Thực hiện các nhiệm vụ khác theo sự chỉ đạo của cấp lãnh đạo trực tiếp, Phó Giám\r\nđốc/Giám đốc Khối PC&KSTT và các cấp lãnh đạo khác, và theo quy định của VPBank trong\r\ntừng thời kỳ', 20, '2025-08-06', 'Đã duyệt', '89 Láng Hạ, Quận Đống Đa, Hà Nội', 'Full-time', 'Không giới hạn', 0, 'bo_mon_luat', 'Đại học'),
(63, 'TD17478557126024', 'Chuyên Viên C&B - Cầu Giấy', 46, 'A. Mô Tả Công Việc:\r\n\r\n1. Thực hiện hệ thống lương, thưởng và các chế độ đãi ngộ khác theo quy định của Công ty\r\n\r\n- Thực hiện công tác quản lý BHXH cho CBNV;\r\n\r\n- Tổ chức thực hiện công tác Quản lý hồ sơ lao động của CBNV;\r\n\r\n- Tổ chức theo dõi và ký Hợp đồng lao động sau khi hết thời gian thử việc cho người lao động;\r\n\r\n- Tổ chức xử lý và quản lý các hợp đồng, quyết định của nhân viên;\r\n\r\n- Thực hiện công tác đăng ký Mã số thuế thu nhập cá nhân, đăng ký giảm trừ gia cảnh cho CBNV;\r\n\r\n- Thực hiện công tác tổng hợp công, tính lương và chi trả lương hàng tháng;\r\n\r\n- Thực hiện việc theo dõi diễn biến quá trình công tác/ vị trí/ lương... của CBNV\r\n\r\n2. Quản lý hồ sơ, chứng từ:\r\n\r\n- Tham gia quản lý hồ sơ nhân viên một cách khoa học, hiệu quả, chính xác.\r\n\r\n3. Các nhiệm vụ khác: Thực hiện các công việc khác do Trưởng nhóm C&B/TP NS phân công.\r\n\r\nB. Quyền Lợi:\r\n\r\n- Mức Lương: từ 10-15tr ( Thỏa thuận theo năng lực)\r\n\r\n- Làm giờ hành chính: 8h-17h45,nghỉ T7 & CN.\r\n\r\n- Được cấp Laptop riêng của công ty.\r\n\r\n- Chế độ tăng lương, thưởng theo quy định Công ty\r\n\r\n- Cấp thẻ giảm giá sử dụng dịch vụ tại hơn 200 nhà hàng của Công Ty.\r\n\r\n- Chế độ hiếu,hỉ,sinh nhật,Lễ-Tết.\r\n\r\n- Có các chương trình đào tạo kỹ năng hàng tháng.\r\n\r\n- Ký hợp đồng lao động chính thức, tham gia đầy đủ BHYT, BHXH theo quy định.\r\n\r\n- Được thường xuyên tham gia các khoá đào tạo, teambuilding hằng năm.\r\n\r\n- Công việc ổn định, môi trường làm việc năng động, sáng tạo\r\n\r\n- Cơ hội thăng tiến cao (nhiều vị trí, không giới hạn thời gian).', 5, '2025-07-31', 'Đã duyệt', 'Tầng 25 tòa nhà Handico, Số 3 Mễ Trì Hạ, Nam Từ Liêm, Hà Nội', 'Full-time', 'Không giới hạn', 0, 'bo_mon_luat', 'Đại học'),
(64, 'TD17478560077879', 'Phát Triển Dự Án Tín Chỉ Các-Bon (Lĩnh Vực Biến Đổi Khí Hậu, Thị Trường Các-Bon Và Chuyển Dịch Năng Lượng)', 47, 'Với vai trò này, bạn sẽ đảm nhận và tham gia các hoạt động sau (không nhất thiết phải tham gia tất cả các hoạt động mà các nhiệm vụ cụ thể sẽ được phân công phù hợp với ngành học và kinh nghiệm):\r\n\r\nPhát triển, đăng ký các dự án tín chỉ các-bon theo các các cơ chế CDM, VCS, GS, I- REC và các cơ chế khí hậu mới trong khuôn khổ Thỏa thuận Paris.\r\nQuản lý các dự án tín chỉ các-bon sau khi được đăng ký để đáp ứng các yêu cầu về đo đạc, báo cáo và thẩm định (MRV) và thực hiện các thủ tục liên quan để thẩm tra và ban hành tín chỉ các-bon cho các dự án;\r\nXây dựng các dự án tín chỉ các-bon rừng/nông nghiệp tại Việt Nam và Đông Nam Á, bao gồm: ARR, IMF, REDD,...\r\nTham gia thực hiện các hoạt động tư vấn chính sách quốc gia liên quan đến giảm phát thải KNK ở cấp quốc gia và ngành, xây dựng/phát triển thị trường các-bon Việt Nam và các chính sách liên quan đến chuyển dịch năng lượng và tiết kiệm năng lượng;\r\nXây dựng dữ liệu, phân tích thông tin tư vấn cho doanh nghiệp liên quan đến giảm phát thải KNK, rủi ro khí hậu và tham gia thị trường các-bon;\r\nLiên hệ với các bên liên quan để tìm phát triển dự án và các hoạt động liên quan trong các lĩnh vực tiềm năng;\r\nDịch thuật và phiên dịch từ tiếng Việt sang tiếng Anh và ngược lại theo yêu cầu;\r\nThực hiện các công việc khác theo yêu cầu của Ban giám đốc Công ty.', 2, '2025-07-16', 'Đã duyệt', 'Tòa Diamond Flower, số 48 Lê Văn Lương, Thanh Xuân, Hà Nội', 'Full-time', 'Không giới hạn', 0, 'bien_doi_khi_hau', 'Đại học'),
(65, 'TD17478563702241', 'Nhân Viên Tư Vấn Triển Khai Phần Mềm Tiếng Nhật', 48, 'Triển khai, cài đặt phần mềm quản lý sản xuất cho các doanh nghiệp, nhà máy.\r\nĐào tạo và hướng dẫn khách hàng sử dụng phần mềm.\r\nTiếp nhận, hỗ trợ và xử lý các yêu cầu trong quá trình triển khai.\r\nGiải đáp các thắc mắc, khiếu nại của khách hàng và hỗ trợ về dịch vụ kỹ thuật qua Internet (Team viewer, Ultraviewer, Zalo).\r\nTổng kết và báo cáo các số liệu liên quan đến công việc', 2, '2025-07-24', 'Đã duyệt', 'Tầng 7, Tòa nhà 170 đường Trần Duy Hưng, Phường Trung Hòa, Quận Cầu Giấy, Thành phố Hà Nội, Việt Nam', 'Full-time', 'Không giới hạn', 0, 'ngoai_ngu', 'Đại học'),
(66, 'TD17478565643438', 'Biên Phiên Dịch Tiếng Anh', 49, 'Chuyển thông tin, yêu cầu báo giá từ khách hàng đến nhà cung cấp nước ngoài\r\n\r\n- Dịch báo giá, hợp đồng liên quan đến thiết bị, vật tư cho dây chuyển sản xuất ngành gạch, gốm, sứ,...\r\n\r\n- Theo dõi đơn hàng, tình hình giao hàng\r\n\r\n- Phiên dịch tại nhà máy cho các chuyên gia khi có chuyên gia nước ngoài sang hướng dẫn lắp đặt tại nhà máy\r\n\r\n- Thực hiện các công việc liên quan đến nghiệp vụ nhập khẩu hàng hoá\r\n\r\n- Báo cáo hàng ngày tình hình công việc\r\n\r\n- Thực hiện các công việc khác theo yêu cầu của Ban Lãnh đạo\r\n\r\n- Thời gian làm việc: từ 8:00 - 12:00, 13:30 - 17:30 từ thứ 2 - sáng thứ 7', 3, '2025-07-24', 'Đã duyệt', 'B1709 MATRIX ONE -NAM TỪ LIÊM - HÀ NỘ', 'Full-time', 'Không giới hạn', 0, 'ngoai_ngu', 'Đại học'),
(67, 'TD17478569136040', 'Kỹ Sư Công Nghệ Môi Trường', 50, 'Tham gia khảo sát các doanh nghiệp có nhu cầu xử lý môi trường khi có yêu cầu\r\nTính toán lên phương án công nghệ, lập sơ đồ P&ID, shop, bóc tách vật tư, thiết bị và dự toán cho hệ thống xử lý nước thải\r\nTham gia vận hành hệ thống xử lý nước sau khi hoàn thành\r\nTriển khai các bản vẽ chi tiết để thực hiện dự án\r\nLập các hồ sơ dự toán, dự thầu, khai toán dự án, thực hiện dự án.', 15, '2025-07-25', 'Đã duyệt', 'Phòng 1908, Tòa nhà Charmvit Tower, Số 117 Trần Duy Hưng, Phường Trung Hoà, Quận Cầu Giấy, Thành phố Hà Nội', 'Full-time', 'Không giới hạn', 0, 'moi_truong', 'Đại học'),
(68, 'TD17478572405003', 'Kế Toán Tổng Hợp', 51, 'Thu thập và xử lý dữ liệu: Thu thập, xử lý và lưu trữ dữ liệu từ các chứng từ kế toán phát sinh hàng ngày.\r\n\r\nLập chứng từ kế toán: Lập chứng từ kế toán ngay tại thời điểm phát sinh nghiệp vụ, bao gồm: phiếu thu - chi, phiếu xuất - nhập kho, hóa đơn mua - bán hàng, chứng từ ngân hàng, phiếu tạm ứng lương, v.vv..\r\n\r\nNhập dữ liệu kế toán: Ghi nhận và cập nhật dữ liệu vào sổ sách kế toán như: sổ quỹ, sổ tiền gửi ngân hàng, sổ công nợ, v.vv..\r\n\r\nKiểm tra tính hợp lệ của hóa đơn, chứng từ: Kiểm tra tính hợp lệ của các chứng từ, hóa đơn trước khi nhập vào sổ sách kế toán.\r\n\r\nQuản lý công nợ: Theo dõi và quản lý công nợ của bên thứ ba như nhà cung cấp, đại lý, chi nhánh, khách hàng, v.vv..\r\n\r\nKê khai thuế: Lập tờ khai các loại thuế như: thuế giá trị gia tăng, thuế thu nhập doanh nghiệp, thuế thu nhập cá nhân và các loại thuế khác theo yêu cầu của cơ quan thuế.\r\n\r\nKê khai hóa đơn, chứng từ: Thực hiện kê khai, đối chiếu các hóa đơn, chứng từ phát sinh trong tháng (bao gồm của cá nhân và tập thể) và lập báo cáo về tình hình sử dụng hóa đơn.\r\n\r\nTính giá trị hàng hóa/sản phẩm/dịch vụ của công ty: Tính giá trị hàng hóa tồn kho, giá vốn hàng bán ra, giá trị của các sản phẩm/dịch vụ của công ty và khấu hao tài sản cố định, v.vv..\r\n\r\nLập báo cáo: Lập báo cáo tháng theo yêu cầu của cấp trên như báo cáo hoạt động kinh doanh, bảng cân đối kế toán, báo cáo lưu chuyển tiền tệ, v.vv..', 3, '2025-07-25', 'Đã duyệt', 'Hà Nội: 106 Hoàng Quốc Việt, P.Nghĩa Tân, Cầu Giấy', 'Full-time', 'Không giới hạn', 0, 'kinh_te', 'Đại học');

-- --------------------------------------------------------

--
-- Table structure for table `ung_tuyen`
--

CREATE TABLE `ung_tuyen` (
  `id` int(11) NOT NULL,
  `ma_tuyen_dung` varchar(50) NOT NULL,
  `stt_sv` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `so_dien_thoai` varchar(20) NOT NULL,
  `thu_gioi_thieu` text DEFAULT NULL,
  `cv_path` varchar(255) NOT NULL,
  `ngay_ung_tuyen` datetime NOT NULL,
  `trang_thai` enum('Chờ duyệt','Đồng ý','Không đồng ý','Hoàn thành') DEFAULT 'Chờ duyệt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ung_tuyen`
--

INSERT INTO `ung_tuyen` (`id`, `ma_tuyen_dung`, `stt_sv`, `ho_ten`, `email`, `so_dien_thoai`, `thu_gioi_thieu`, `cv_path`, `ngay_ung_tuyen`, `trang_thai`) VALUES
(14, 'TD17476276377537', 67, 'Vũ Thảo Vân', 'vuthaovan088@gmail.com', '1234267890', '', '../Uploads/cv/1747678409_1745383675_CV_Vu___Tha__o_Va__n_-_CV-KHCN-VU___THA__O_VA__N-TopCV.vn__1_.pdf', '2025-05-20 01:13:29', 'Hoàn thành'),
(15, 'TD17476286528372', 87, 'Nguyễn Công Hiếu', '22111061159@hunre.edu.vn', '0987654321', '', '../Uploads/cv/1747682473_CV-_Nguy___n_Th____H___ng_-_TTSPL.pdf', '2025-05-20 02:21:13', 'Đồng ý'),
(17, 'TD17478529631704', 93, 'Hà Minh Quang', '22111061425@hunre.edu.vn', '0985113229', '', '../Uploads/cv/1748900073_NT_H___ng_Nga_CV.pdf', '2025-06-03 04:34:33', 'Chờ duyệt'),
(18, 'TD17478527738344', 94, 'Chử Trung Huân', '22111061173@hunre.edu.vn', '0985113776', '', '../Uploads/cv/1748900100_NT_H___ng_Nga_CV.pdf', '2025-06-03 04:35:00', 'Chờ duyệt'),
(19, 'TD17478449812802', 77, 'Phạm Tiến Đạt', '22111061353@hunre.edu.vn', '0334567890', '', '../Uploads/cv/1748902502_CTV-Pham-Hong-Hanh-TopCV.vn-170325.80447.pdf', '2025-06-03 05:15:02', 'Đồng ý'),
(20, 'TD17476301692948', 64, 'TRẦN THI NHƯ HOA', 'trannhuhoa28@gmail.com', '1234567890', '', '../Uploads/cv/1748929821_CV_TRAN_THI_NHU_HOA_NHAN_VIEN_PHAP_CHE.pdf', '2025-06-03 12:50:21', 'Đồng ý'),
(21, 'TD17478531671651', 98, 'Nguyễn Thị Ngọc Khánh', '22111060953@hunre.edu.vn', '0945845845', '', '../Uploads/cv/1748930043_NT_H___ng_Nga_CV.pdf', '2025-06-03 12:54:03', 'Chờ duyệt'),
(22, 'TD17478527738344', 99, 'Nguyễn Sỹ Kiên', '22111061006@hunre.edu.vn', '0911322244', '', '../Uploads/cv/1748930161_D____ng_Minh_Th__y_-_Vi___t_Content_Ph__p_l__.pdf', '2025-06-03 12:56:01', 'Đồng ý'),
(23, 'TD17476269602351', 90, 'Vũ Thị Minh Tuyết', '22111061363@hunre.edu.vn', '0366015791', '', '../Uploads/cv/1748942685_CV_TRAN_THI_NHU_HOA_NHAN_VIEN_PHAP_CHE.pdf', '2025-06-03 16:24:45', 'Đồng ý');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bao_cao_thuc_tap`
--
ALTER TABLE `bao_cao_thuc_tap`
  ADD PRIMARY KEY (`stt_baocao`),
  ADD KEY `ma_dang_ky` (`ma_dang_ky`);

--
-- Indexes for table `cong_ty`
--
ALTER TABLE `cong_ty`
  ADD PRIMARY KEY (`stt_cty`),
  ADD UNIQUE KEY `ten_cong_ty` (`ten_cong_ty`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `co_so_thuc_tap`
--
ALTER TABLE `co_so_thuc_tap`
  ADD PRIMARY KEY (`stt_cstt`),
  ADD UNIQUE KEY `ma_co_so` (`ma_co_so`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `danh_gia_thuc_tap`
--
ALTER TABLE `danh_gia_thuc_tap`
  ADD PRIMARY KEY (`stt_danhgia`),
  ADD KEY `ma_dang_ky` (`ma_dang_ky`),
  ADD KEY `stt_sv` (`stt_sv`),
  ADD KEY `stt_cstt` (`stt_cstt`);

--
-- Indexes for table `giang_vien`
--
ALTER TABLE `giang_vien`
  ADD PRIMARY KEY (`stt_gv`),
  ADD UNIQUE KEY `so_hieu_giang_vien` (`so_hieu_giang_vien`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `lich_thuc_tap`
--
ALTER TABLE `lich_thuc_tap`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ma_dang_ky` (`ma_dang_ky`),
  ADD KEY `stt_sv` (`stt_sv`);

--
-- Indexes for table `pdf_nhan`
--
ALTER TABLE `pdf_nhan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stt_danhgia` (`stt_danhgia`);

--
-- Indexes for table `sinh_vien`
--
ALTER TABLE `sinh_vien`
  ADD PRIMARY KEY (`stt_sv`),
  ADD UNIQUE KEY `ma_sinh_vien` (`ma_sinh_vien`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `ma_sinh_vien_2` (`ma_sinh_vien`),
  ADD KEY `so_hieu` (`so_hieu`);

--
-- Indexes for table `tuyen_dung`
--
ALTER TABLE `tuyen_dung`
  ADD PRIMARY KEY (`stt_tuyendung`),
  ADD UNIQUE KEY `ma_tuyen_dung` (`ma_tuyen_dung`),
  ADD KEY `stt_cty` (`stt_cty`);

--
-- Indexes for table `ung_tuyen`
--
ALTER TABLE `ung_tuyen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ma_tuyen_dung` (`ma_tuyen_dung`),
  ADD KEY `stt_sv` (`stt_sv`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bao_cao_thuc_tap`
--
ALTER TABLE `bao_cao_thuc_tap`
  MODIFY `stt_baocao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `cong_ty`
--
ALTER TABLE `cong_ty`
  MODIFY `stt_cty` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `co_so_thuc_tap`
--
ALTER TABLE `co_so_thuc_tap`
  MODIFY `stt_cstt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `danh_gia_thuc_tap`
--
ALTER TABLE `danh_gia_thuc_tap`
  MODIFY `stt_danhgia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `giang_vien`
--
ALTER TABLE `giang_vien`
  MODIFY `stt_gv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `lich_thuc_tap`
--
ALTER TABLE `lich_thuc_tap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pdf_nhan`
--
ALTER TABLE `pdf_nhan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `sinh_vien`
--
ALTER TABLE `sinh_vien`
  MODIFY `stt_sv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `tuyen_dung`
--
ALTER TABLE `tuyen_dung`
  MODIFY `stt_tuyendung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `ung_tuyen`
--
ALTER TABLE `ung_tuyen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bao_cao_thuc_tap`
--
ALTER TABLE `bao_cao_thuc_tap`
  ADD CONSTRAINT `bao_cao_thuc_tap_ibfk_1` FOREIGN KEY (`ma_dang_ky`) REFERENCES `ung_tuyen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `danh_gia_thuc_tap`
--
ALTER TABLE `danh_gia_thuc_tap`
  ADD CONSTRAINT `fk_danhgia_coso` FOREIGN KEY (`stt_cstt`) REFERENCES `co_so_thuc_tap` (`stt_cstt`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_danhgia_sinhvien` FOREIGN KEY (`stt_sv`) REFERENCES `sinh_vien` (`stt_sv`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_danhgia_ungtuyen` FOREIGN KEY (`ma_dang_ky`) REFERENCES `ung_tuyen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lich_thuc_tap`
--
ALTER TABLE `lich_thuc_tap`
  ADD CONSTRAINT `lich_thuc_tap_ibfk_1` FOREIGN KEY (`ma_dang_ky`) REFERENCES `ung_tuyen` (`id`),
  ADD CONSTRAINT `lich_thuc_tap_ibfk_2` FOREIGN KEY (`stt_sv`) REFERENCES `sinh_vien` (`stt_sv`);

--
-- Constraints for table `pdf_nhan`
--
ALTER TABLE `pdf_nhan`
  ADD CONSTRAINT `pdf_nhan_ibfk_1` FOREIGN KEY (`stt_danhgia`) REFERENCES `danh_gia_thuc_tap` (`stt_danhgia`) ON DELETE CASCADE;

--
-- Constraints for table `sinh_vien`
--
ALTER TABLE `sinh_vien`
  ADD CONSTRAINT `fk_sv_gv` FOREIGN KEY (`so_hieu`) REFERENCES `giang_vien` (`so_hieu_giang_vien`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tuyen_dung`
--
ALTER TABLE `tuyen_dung`
  ADD CONSTRAINT `tuyen_dung_ibfk_1` FOREIGN KEY (`stt_cty`) REFERENCES `cong_ty` (`stt_cty`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ung_tuyen`
--
ALTER TABLE `ung_tuyen`
  ADD CONSTRAINT `ung_tuyen_ibfk_1` FOREIGN KEY (`ma_tuyen_dung`) REFERENCES `tuyen_dung` (`ma_tuyen_dung`),
  ADD CONSTRAINT `ung_tuyen_ibfk_2` FOREIGN KEY (`stt_sv`) REFERENCES `sinh_vien` (`stt_sv`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
