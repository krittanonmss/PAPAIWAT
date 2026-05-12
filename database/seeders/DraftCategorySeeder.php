<?php

namespace Database\Seeders;

use App\Models\Content\Category;
use Illuminate\Database\Seeder;

class DraftCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Temple categories
            ['name' => 'วัดในป่า', 'slug' => 'forest-temples', 'type_key' => 'temple', 'description' => 'วัดบรรยากาศร่มรื่น เงียบสงบ เหมาะสำหรับพักใจและใกล้ชิดธรรมชาติ'],
            ['name' => 'วัดปฏิบัติธรรม', 'slug' => 'meditation-temples', 'type_key' => 'temple', 'description' => 'วัดที่เหมาะสำหรับฝึกสมาธิ เดินจงกรม และใช้เวลาทบทวนตนเอง'],
            ['name' => 'วัดสายมู', 'slug' => 'spiritual-temples', 'type_key' => 'temple', 'description' => 'วัดที่นิยมไปขอพร เสริมดวง ไหว้สิ่งศักดิ์สิทธิ์ และเสริมกำลังใจ'],
            ['name' => 'วัดชมวิว', 'slug' => 'scenic-view-temples', 'type_key' => 'temple', 'description' => 'วัดที่มีจุดชมวิวสวย เหมาะสำหรับเที่ยว ถ่ายรูป และพักผ่อน'],
            ['name' => 'วัดบนเขา', 'slug' => 'hill-temples', 'type_key' => 'temple', 'description' => 'วัดที่ตั้งอยู่บนเนินเขาหรือภูเขา บรรยากาศโปร่งและเห็นวิวกว้าง'],
            ['name' => 'วัดริมแม่น้ำ', 'slug' => 'riverside-temples', 'type_key' => 'temple', 'description' => 'วัดริมแม่น้ำหรือใกล้น้ำ เหมาะสำหรับเดินเล่น ทำบุญ และชมบรรยากาศ'],
            ['name' => 'วัดเงียบสงบ', 'slug' => 'peaceful-temples', 'type_key' => 'temple', 'description' => 'วัดที่เหมาะสำหรับคนอยากหลีกความวุ่นวายและใช้เวลาอยู่กับตัวเอง'],
            ['name' => 'วัดสำหรับครอบครัว', 'slug' => 'family-friendly-temples', 'type_key' => 'temple', 'description' => 'วัดที่เดินทางง่าย บรรยากาศดี เหมาะกับการพาครอบครัวไปทำบุญ'],
            ['name' => 'วัดน่าเที่ยววันหยุด', 'slug' => 'weekend-temples', 'type_key' => 'temple', 'description' => 'วัดที่เหมาะสำหรับจัดทริปสั้น ๆ ในวันหยุดหรือช่วงพักผ่อน'],
            ['name' => 'วัดถ่ายรูปสวย', 'slug' => 'photo-spot-temples', 'type_key' => 'temple', 'description' => 'วัดที่มีมุมถ่ายรูปสวย บรรยากาศดี และเหมาะสำหรับเก็บภาพความประทับใจ'],
            ['name' => 'วัดใกล้เมือง', 'slug' => 'city-nearby-temples', 'type_key' => 'temple', 'description' => 'วัดที่เดินทางสะดวก เหมาะสำหรับแวะทำบุญหรือพักใจระหว่างวัน'],
            ['name' => 'วัดธรรมชาติ', 'slug' => 'nature-temples', 'type_key' => 'temple', 'description' => 'วัดที่มีพื้นที่ธรรมชาติ ต้นไม้ บึงน้ำ หรือบรรยากาศร่มรื่น'],
            ['name' => 'วัดถ้ำ', 'slug' => 'cave-temples', 'type_key' => 'temple', 'description' => 'วัดที่มีถ้ำหรือพื้นที่ธรรมชาติพิเศษ เหมาะสำหรับสายสงบและสายผจญภัยเบา ๆ'],
            ['name' => 'วัดทำบุญไหว้พระ', 'slug' => 'merit-making-temples', 'type_key' => 'temple', 'description' => 'วัดที่เหมาะสำหรับทำบุญ ไหว้พระ และเริ่มต้นวันด้วยความสบายใจ'],
            ['name' => 'วัดเดินเล่นสงบใจ', 'slug' => 'relaxing-walk-temples', 'type_key' => 'temple', 'description' => 'วัดที่เหมาะสำหรับเดินเล่นเบา ๆ ชมบรรยากาศ และปล่อยใจให้สงบ'],
            ['name' => 'วัดชุมชนน่าแวะ', 'slug' => 'local-community-temples', 'type_key' => 'temple', 'description' => 'วัดในชุมชนที่มีเสน่ห์ เรียบง่าย และสะท้อนวิถีชีวิตท้องถิ่น'],
            ['name' => 'วัดเหมาะกับมือใหม่เข้าวัด', 'slug' => 'beginner-friendly-temples', 'type_key' => 'temple', 'description' => 'วัดที่บรรยากาศเป็นกันเอง เหมาะสำหรับคนที่เริ่มสนใจเข้าวัดหรือทำบุญ'],
            ['name' => 'วัดพักใจหลังทำงาน', 'slug' => 'after-work-temples', 'type_key' => 'temple', 'description' => 'วัดที่เหมาะสำหรับแวะพักใจหลังเลิกงานหรือช่วงที่ต้องการความสงบ'],
            ['name' => 'วัดบรรยากาศดี', 'slug' => 'good-atmosphere-temples', 'type_key' => 'temple', 'description' => 'วัดที่มีบรรยากาศน่าไป เดินสบาย และเหมาะกับการใช้เวลาช้า ๆ'],
            ['name' => 'วัดสำหรับทริปสั้น', 'slug' => 'short-trip-temples', 'type_key' => 'temple', 'description' => 'วัดที่เหมาะสำหรับแวะเที่ยวไม่นาน เดินทางง่าย และจัดเข้าทริปได้สะดวก'],

            // Article categories
            ['name' => 'ธรรมะอ่านง่าย', 'slug' => 'easy-dhamma', 'type_key' => 'article', 'description' => 'บทความธรรมะภาษาง่าย อ่านแล้วเข้าใจและนำไปใช้ได้จริง'],
            ['name' => 'ธรรมะใช้ในชีวิตประจำวัน', 'slug' => 'daily-life-dhamma', 'type_key' => 'article', 'description' => 'ข้อคิดธรรมะสำหรับการทำงาน ความสัมพันธ์ และการใช้ชีวิตทั่วไป'],
            ['name' => 'สติและการรู้ใจตัวเอง', 'slug' => 'mindfulness-and-self-awareness', 'type_key' => 'article', 'description' => 'บทความเกี่ยวกับการฝึกสติ รู้ทันอารมณ์ และกลับมาอยู่กับปัจจุบัน'],
            ['name' => 'สมาธิสำหรับมือใหม่', 'slug' => 'meditation-for-beginners', 'type_key' => 'article', 'description' => 'บทความสำหรับผู้เริ่มต้นฝึกสมาธิ การหายใจ และการนั่งสงบ'],
            ['name' => 'ข้อคิดก่อนนอน', 'slug' => 'bedtime-reflections', 'type_key' => 'article', 'description' => 'บทความสั้น ๆ สำหรับอ่านก่อนนอน เพื่อปล่อยวางและพักใจ'],
            ['name' => 'ข้อคิดเริ่มต้นวันใหม่', 'slug' => 'morning-reflections', 'type_key' => 'article', 'description' => 'บทความสำหรับเริ่มต้นวันด้วยใจสงบ มีสติ และไม่เร่งรีบเกินไป'],
            ['name' => 'เมตตาและการให้อภัย', 'slug' => 'kindness-and-forgiveness', 'type_key' => 'article', 'description' => 'บทความเกี่ยวกับความเมตตา การให้อภัย และการอยู่ร่วมกับผู้อื่น'],
            ['name' => 'ปล่อยวางความกังวล', 'slug' => 'letting-go-of-worry', 'type_key' => 'article', 'description' => 'บทความช่วยทบทวนความกังวล ความคาดหวัง และสิ่งที่ควบคุมไม่ได้'],
            ['name' => 'ชีวิตไม่ประมาท', 'slug' => 'mindful-living', 'type_key' => 'article', 'description' => 'ข้อคิดสำหรับการใช้ชีวิตอย่างรอบคอบ มีสติ และเห็นคุณค่าของเวลา'],
            ['name' => 'ศาสนาเข้าใจง่าย', 'slug' => 'simple-religion', 'type_key' => 'article', 'description' => 'บทความอธิบายเรื่องศาสนาแบบเข้าใจง่าย ไม่ซับซ้อน'],
            ['name' => 'ความหมายของการทำบุญ', 'slug' => 'meaning-of-merit-making', 'type_key' => 'article', 'description' => 'บทความเกี่ยวกับการทำบุญ การให้ทาน และคุณค่าทางใจ'],
            ['name' => 'วันสำคัญทางศาสนา', 'slug' => 'buddhist-holy-days', 'type_key' => 'article', 'description' => 'บทความเกี่ยวกับวันสำคัญทางพระพุทธศาสนาและความหมายที่ควรรู้'],
            ['name' => 'การเข้าวัดอย่างเหมาะสม', 'slug' => 'temple-visit-guide', 'type_key' => 'article', 'description' => 'คำแนะนำเกี่ยวกับมารยาท การแต่งกาย และการปฏิบัติตัวเมื่อไปวัด'],
            ['name' => 'วัดกับวิถีชุมชน', 'slug' => 'temple-and-community-life', 'type_key' => 'article', 'description' => 'บทความเกี่ยวกับบทบาทของวัดต่อชุมชน วัฒนธรรม และชีวิตคนไทย'],
            ['name' => 'ฟังธรรมให้เข้าใจ', 'slug' => 'understanding-dhamma-talks', 'type_key' => 'article', 'description' => 'บทความช่วยให้การฟังธรรมง่ายขึ้น และนำข้อคิดไปใช้ได้จริง'],
            ['name' => 'ธรรมะสำหรับคนทำงาน', 'slug' => 'dhamma-for-working-life', 'type_key' => 'article', 'description' => 'ข้อคิดสำหรับรับมือความเครียด งาน ความรับผิดชอบ และความสัมพันธ์ในที่ทำงาน'],
            ['name' => 'ธรรมะเยียวยาใจ', 'slug' => 'healing-dhamma', 'type_key' => 'article', 'description' => 'บทความสำหรับช่วงเวลาที่เหนื่อย ท้อ กังวล หรืออยากกลับมาดูแลใจ'],
            ['name' => 'ศีลและการใช้ชีวิต', 'slug' => 'precepts-and-living', 'type_key' => 'article', 'description' => 'บทความเกี่ยวกับศีลในมุมที่นำมาใช้กับชีวิตยุคปัจจุบันได้'],
            ['name' => 'คำสอนน่าคิด', 'slug' => 'thoughtful-teachings', 'type_key' => 'article', 'description' => 'ข้อคิดและคำสอนที่ช่วยให้มองชีวิตอย่างใจเย็นขึ้น'],
            ['name' => 'บทความอ่านสั้นพักใจ', 'slug' => 'short-peaceful-reads', 'type_key' => 'article', 'description' => 'บทความสั้น อ่านง่าย เหมาะสำหรับพักใจระหว่างวัน'],
        ];

        foreach ($categories as $index => $item) {
            Category::query()->updateOrCreate(
                [
                    'parent_id' => null,
                    'slug' => $item['slug'],
                    'type_key' => $item['type_key'],
                ],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'level' => 0,
                    'sort_order' => $index + 1,
                    'status' => 'active',
                    'is_featured' => true,
                    'meta_title' => $item['name'],
                    'meta_description' => $item['description'],
                    'created_by_admin_id' => null,
                    'updated_by_admin_id' => null,
                ]
            );
        }
    }
}