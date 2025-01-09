import { router } from "@inertiajs/react"; //router: ใช้สำหรับทำการเปลี่ยนหน้าโดยไม่ต้องรีเฟรชหน้าใหม่ (จาก Inertia.js)
import { useState } from "react"; //useState: React hook ใช้สำหรับจัดการสถานะ (state) ของข้อมูลใน component
import './Index.css'; // นำเข้าไฟล์ CSS

// query = ค่าของการค้นหาที่ส่งกลับจาก controller
// employees = ข้อมูลพนักงานที่ส่งกลับมาจาก controller
export default function Index({ employees, query }) {
    // กำหนดค่าเริ่มต้นของการค้นหาให้เป็น query ที่รับมาจาก controller (ถ้ามี) หรือค่าว่าง
    const [search, setSearch] = useState(query || '');

    // ฟังก์ชันในการค้นหาพนักงานตามคำที่กรอก
    const handleSearch = (e) => {
        e.preventDefault();  // หยุดการโหลดหน้าใหม่เมื่อกดปุ่ม Submit
        // ส่งค่า search ไปที่ route '/employee' เพื่อค้นหาพนักงาน
        router.get('/employee', { search });
    };

    // ฟังก์ชันในการรีเซ็ตการค้นหา
    const handleRefresh = () => {
        // รีเซ็ตการค้นหากลับเป็นค่าว่าง
        router.get('/employee', { search: '' });
        setSearch('');  // ล้างค่า search ใน state
    };

    // ฟังก์ชันในการเปลี่ยนหน้าและส่งค่า search ไปด้วย
    const handlePageChange = (url) => {
        if (url) { //ตรวจสอบว่า URL สำหรับหน้าถัดไปหรือหน้าก่อนหน้ามีหรือไม่
            // เปลี่ยนหน้าโดยไม่ทำให้การค้นหาหายไป
            router.get(url, { search });
        }
    };

    return (
        <div className="container">
            <h1>Employee List</h1>
            {/* ฟอร์มสำหรับการค้นหาพนักงาน */}
            <form onSubmit={handleSearch} className="search-form"> 
                <input 
                    type="text" 
                    value={search}  // ค่าของ input จะถูกผูกกับ state search
                    onChange={(e) => setSearch(e.target.value)}  // อัปเดต state search เมื่อผู้ใช้พิมพ์
                    placeholder="Search employees"
                />
                <button type="submit">Search</button>  {/* ปุ่มสำหรับส่งฟอร์มเพื่อค้นหาพนักงาน */}
                <button type="button" onClick={handleRefresh}>Refresh</button> {/* ปุ่มสำหรับรีเซ็ตการค้นหา */}
            </form>
            <div className="table-container">  {/* แสดงตารางพนักงานโดยใช้ข้อมูลจาก employees.data */}
                <table className="employee-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Last Name</th>
                            <th>Age</th>
                        </tr>
                    </thead>
                    <tbody>
                        {/* แสดงข้อมูลพนักงานที่ค้นหามาจาก server */}
                        {employees.data.map((employee) => (
                            <tr key={employee.emp_no}> {/* ใช้ emp_no เป็น key เพื่อให้ React จัดการการเปลี่ยนแปลงใน DOM ได้อย่างมีประสิทธิภาพ*/}
                                <td>{employee.emp_no}</td>
                                <td>{employee.first_name}</td>
                                <td>{employee.last_name}</td>
                                <td>{employee.birth_date}</td> 
                            </tr> //แสดงข้อมูลพนักงานแต่ละคนในแต่ละเซลล์ของตาราง
                        ))}
                    </tbody>
                </table>
            </div>
            <div className="pagination">
                {/* ปุ่มสำหรับไปยังหน้าก่อนหน้า */}
                <button
                    onClick={() => handlePageChange(employees.prev_page_url)}  // ส่ง URL ของหน้าก่อนหน้า
                    disabled={!employees.prev_page_url}  // ปิดใช้งานปุ่มถ้าไม่มีหน้าก่อนหน้า
                >
                    Previous
                </button>
                {/* แสดงหมายเลขหน้าและจำนวนหน้าทั้งหมด */}
                <span>Page {employees.current_page} of {employees.last_page}</span> 
                {/* ปุ่มสำหรับไปยังหน้าถัดไป */}
                <button
                    onClick={() => handlePageChange(employees.next_page_url)}  // ส่ง URL ของหน้าถัดไป
                    disabled={!employees.next_page_url}  // ปิดใช้งานปุ่มถ้าไม่มีหน้าถัดไป
                >
                    Next
                </button>
            </div>
        </div>
    );
}