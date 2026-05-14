<?php

namespace Database\Seeders;

use App\Models\Content\Category;
use App\Models\Content\Content;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentCategoryAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = $this->seedCategories();

        Content::query()
            ->whereIn('content_type', ['temple', 'article'])
            ->orderBy('content_type')
            ->orderBy('id')
            ->get()
            ->each(function (Content $content, int $index) use ($categories) {
                $selected = $this->selectCategoriesForContent($content, $categories[$content->content_type], $index);

                if ($selected->count() < 3) {
                    return;
                }

                $categoryIds = $selected->pluck('id')->values();

                DB::table('categorizables')
                    ->where('categorizable_type', Content::class)
                    ->where('categorizable_id', $content->id)
                    ->whereIn('category_id', $categories[$content->content_type]->pluck('id'))
                    ->delete();

                $content->categories()->attach($categoryIds->mapWithKeys(fn ($categoryId, $sortOrder) => [
                    $categoryId => [
                        'is_primary' => $sortOrder === 0,
                        'sort_order' => $sortOrder + 1,
                        'created_at' => now(),
                    ],
                ])->all());
            });
    }

    private function seedCategories(): array
    {
        $categorySets = [
            'temple' => [
                ['name' => 'บรรเทาจิตใจ', 'slug' => 'mind-relief-temples', 'description' => 'วัดที่เหมาะสำหรับพักใจ คลายความกังวล และใช้เวลาสงบกับตัวเอง'],
                ['name' => 'วัดธรรมชาติ', 'slug' => 'nature-temples', 'description' => 'วัดที่มีต้นไม้ พื้นที่สีเขียว หรือบรรยากาศร่มรื่นน่าเดิน'],
                ['name' => 'วัดริมน้ำ', 'slug' => 'riverside-temples', 'description' => 'วัดใกล้น้ำ บรรยากาศเปิดโล่ง เหมาะกับการทำบุญและเดินเล่นช้า ๆ'],
                ['name' => 'วัดปฏิบัติธรรม', 'slug' => 'meditation-temples', 'description' => 'วัดที่เหมาะกับการฝึกสมาธิ เดินจงกรม และทบทวนใจ'],
                ['name' => 'วัดชมวิว', 'slug' => 'scenic-view-temples', 'description' => 'วัดที่มีวิวสวย จุดชมทิวทัศน์ หรือบรรยากาศโปร่งสบาย'],
                ['name' => 'วัดทำบุญวันหยุด', 'slug' => 'weekend-merit-temples', 'description' => 'วัดที่เหมาะสำหรับจัดทริปสั้น ทำบุญ และพักผ่อนในวันหยุด'],
                ['name' => 'วัดครอบครัว', 'slug' => 'family-temples', 'description' => 'วัดที่เดินทางง่าย บรรยากาศเป็นมิตร เหมาะกับการพาครอบครัวไปทำบุญ'],
                ['name' => 'วัดสงบใกล้เมือง', 'slug' => 'peaceful-city-temples', 'description' => 'วัดที่แวะง่ายแต่ยังให้ความสงบ เหมาะกับคนเมืองที่อยากพักใจ'],
                ['name' => 'วัดสายศรัทธา', 'slug' => 'faith-temples', 'description' => 'วัดที่เหมาะกับการไหว้พระ ขอพร และเติมกำลังใจ'],
                ['name' => 'วัดวิถีชุมชน', 'slug' => 'community-temples', 'description' => 'วัดที่สะท้อนชีวิตท้องถิ่น งานบุญ และความเรียบง่ายของชุมชน'],
                ['name' => 'วัดถ่ายรูปสวย', 'slug' => 'photo-friendly-temples', 'description' => 'วัดที่มีมุมสวย สถาปัตยกรรมเด่น หรือบรรยากาศน่าเก็บภาพ'],
                ['name' => 'วัดภาวนาเงียบลึก', 'slug' => 'quiet-retreat-temples', 'description' => 'วัดที่เหมาะกับคนอยากหลีกความวุ่นวายและอยู่กับความเงียบ'],
            ],
            'article' => [
                ['name' => 'อ่านแล้วใจเบา', 'slug' => 'light-heart-reads', 'description' => 'บทความที่ช่วยให้ใจเบาลง คลายความกังวล และกลับมาหายใจได้ลึกขึ้น'],
                ['name' => 'ธรรมะใช้ได้จริง', 'slug' => 'practical-dhamma', 'description' => 'ข้อคิดธรรมะที่นำไปใช้กับงาน ความสัมพันธ์ และชีวิตประจำวันได้'],
                ['name' => 'พักใจระหว่างวัน', 'slug' => 'midday-mind-breaks', 'description' => 'บทความสั้น อ่านง่าย เหมาะสำหรับหยุดพักใจระหว่างวัน'],
                ['name' => 'สติสำหรับมือใหม่', 'slug' => 'mindfulness-for-beginners', 'description' => 'บทความสำหรับเริ่มฝึกสติ รู้ทันอารมณ์ และกลับมาอยู่กับปัจจุบัน'],
                ['name' => 'ปล่อยวางความกังวล', 'slug' => 'letting-go-of-worry', 'description' => 'บทความที่ช่วยมองความกังวลอย่างเข้าใจและปล่อยสิ่งที่ควบคุมไม่ได้'],
                ['name' => 'เมตตาและให้อภัย', 'slug' => 'kindness-and-forgiveness', 'description' => 'ข้อคิดเรื่องความเมตตา การฟัง และการให้อภัยทั้งผู้อื่นและตัวเอง'],
                ['name' => 'เริ่มวันด้วยสติ', 'slug' => 'mindful-morning', 'description' => 'บทความสำหรับตั้งหลักใจในตอนเช้าและเริ่มต้นวันอย่างไม่เร่งรีบ'],
                ['name' => 'ก่อนนอนปล่อยใจ', 'slug' => 'bedtime-letting-go', 'description' => 'บทความอ่านก่อนนอน เพื่อวางความคิดหนัก ๆ และพักใจ'],
                ['name' => 'เข้าวัดอย่างเข้าใจ', 'slug' => 'temple-visit-guide', 'description' => 'บทความแนะนำมารยาท การทำบุญ และการเข้าวัดอย่างเหมาะสม'],
                ['name' => 'เรื่องบุญที่ใกล้ตัว', 'slug' => 'everyday-merit', 'description' => 'บทความที่เล่าเรื่องบุญ การให้ และความดีในชีวิตประจำวัน'],
                ['name' => 'ศาสนาอ่านง่าย', 'slug' => 'easy-religion', 'description' => 'บทความอธิบายเรื่องศาสนาและวันสำคัญด้วยภาษาที่เข้าถึงง่าย'],
                ['name' => 'ข้อคิดคนทำงาน', 'slug' => 'working-life-dhamma', 'description' => 'ธรรมะสำหรับรับมือความเครียด งาน และความรับผิดชอบในชีวิตทำงาน'],
            ],
        ];

        return collect($categorySets)
            ->map(fn (array $items, string $typeKey) => collect($items)
                ->map(function (array $item, int $index) use ($typeKey) {
                    return Category::query()->updateOrCreate(
                        [
                            'parent_id' => null,
                            'slug' => $item['slug'],
                            'type_key' => $typeKey,
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
                })
                ->values())
            ->all();
    }

    private function selectCategoriesForContent(Content $content, $categories, int $index)
    {
        $text = mb_strtolower(implode(' ', [
            $content->title,
            $content->slug,
            $content->excerpt,
            $content->description,
        ]));

        $keywords = $content->content_type === 'temple'
            ? $this->templeCategoryKeywords()
            : $this->articleCategoryKeywords();

        $selected = collect();

        foreach ($keywords as $slug => $needles) {
            if ($selected->count() >= 3) {
                break;
            }

            if (collect($needles)->contains(fn ($needle) => str_contains($text, mb_strtolower($needle)))) {
                $category = $categories->firstWhere('slug', $slug);

                if ($category && ! $selected->contains('id', $category->id)) {
                    $selected->push($category);
                }
            }
        }

        $offset = $index % max($categories->count(), 1);

        return $selected
            ->merge($categories->slice($offset)->concat($categories->take($offset)))
            ->unique('id')
            ->take(3)
            ->values();
    }

    private function templeCategoryKeywords(): array
    {
        return [
            'mind-relief-temples' => ['พักใจ', 'สงบ', 'ใจสงบ', 'ภาวนา'],
            'nature-temples' => ['ธรรมชาติ', 'ป่า', 'ต้นไม้', 'ร่มรื่น', 'วนาราม'],
            'riverside-temples' => ['ริมน้ำ', 'แม่น้ำ', 'บึง', 'น้ำ'],
            'meditation-temples' => ['ปฏิบัติธรรม', 'สมาธิ', 'จงกรม', 'ภาวนา'],
            'scenic-view-temples' => ['ชมวิว', 'วิว', 'เขา', 'เนินเขา', 'ภูเขา'],
            'weekend-merit-temples' => ['วันหยุด', 'ทริป', 'ทำบุญ'],
            'family-temples' => ['ครอบครัว', 'ผู้สูงอายุ', 'เดินทางง่าย'],
            'peaceful-city-temples' => ['เมือง', 'ใกล้เมือง', 'คนทำงาน'],
            'faith-temples' => ['ศรัทธา', 'ไหว้พระ', 'ขอพร', 'มงคล'],
            'community-temples' => ['ชุมชน', 'ท้องถิ่น', 'งานบุญ', 'ประเพณี'],
            'photo-friendly-temples' => ['ถ่ายรูป', 'สถาปัตยกรรม', 'ลานโพธิ์'],
            'quiet-retreat-temples' => ['ถ้ำ', 'เงียบ', 'หลีก', 'สงบเป็นพิเศษ'],
        ];
    }

    private function articleCategoryKeywords(): array
    {
        return [
            'light-heart-reads' => ['ใจเบา', 'พักใจ', 'เยียวยา', 'เหนื่อย'],
            'practical-dhamma' => ['ชีวิตประจำวัน', 'ใช้ชีวิต', 'นำไปใช้', 'ทำงาน'],
            'midday-mind-breaks' => ['อ่านสั้น', 'ระหว่างวัน', 'พัก'],
            'mindfulness-for-beginners' => ['สติ', 'รู้ทัน', 'สมาธิ', 'มือใหม่'],
            'letting-go-of-worry' => ['ปล่อยวาง', 'กังวล', 'ควบคุมไม่ได้'],
            'kindness-and-forgiveness' => ['เมตตา', 'ให้อภัย', 'ฟัง'],
            'mindful-morning' => ['เริ่มต้นวัน', 'วันใหม่', 'ตอนเช้า'],
            'bedtime-letting-go' => ['ก่อนนอน', 'นอน'],
            'temple-visit-guide' => ['เข้าวัด', 'มารยาท', 'ทำบุญ'],
            'everyday-merit' => ['บุญ', 'ทำบุญ', 'ให้ทาน'],
            'easy-religion' => ['ศาสนา', 'วันสำคัญ', 'พระพุทธศาสนา'],
            'working-life-dhamma' => ['คนทำงาน', 'งาน', 'ความรับผิดชอบ'],
        ];
    }
}
