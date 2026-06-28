<?php
/**
 * Yerel görünüm önizlemesi için sahte (mock) PDO katmanı.
 * Gerçek veritabanına bağlanmadan sayfaların örnek verilerle render edilmesini sağlar.
 * Sadece LOCAL_PREVIEW=1 ortam değişkeni set edildiğinde devreye girer (bkz. db.php).
 */

class PreviewStatement
{
    private array $rows;
    private int $cursor = 0;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function execute(array $params = []): bool
    {
        return true;
    }

    public function fetch(int $mode = PDO::FETCH_ASSOC)
    {
        if (!isset($this->rows[$this->cursor])) {
            return false;
        }
        return $this->rows[$this->cursor++];
    }

    public function fetchAll(int $mode = PDO::FETCH_ASSOC): array
    {
        return $this->rows;
    }
}

class PreviewPDO
{
    private array $properties;
    private array $images;

    public function __construct()
    {
        $this->properties = [
            [
                'id' => 1,
                'title' => 'Deniz Manzaralı Lüks Daire',
                'type' => 'Daire',
                'status' => 'Satılık',
                'price' => 4250000,
                'location' => 'Kadıköy, İstanbul',
                'area' => 145,
                'rooms' => '3+1',
                'description' => "Deniz manzaralı, geniş balkonlu, yeni tadilatlı lüks daire. Site içerisinde otopark ve güvenlik mevcuttur.\nMetro ve sahile yürüme mesafesindedir.",
                'created_at' => '2026-06-20 10:00:00',
            ],
            [
                'id' => 2,
                'title' => 'Merkezi Konumda Kiralık Ofis',
                'type' => 'Ofis',
                'status' => 'Kiralık',
                'price' => 38000,
                'location' => 'Şişli, İstanbul',
                'area' => 90,
                'rooms' => '-',
                'description' => "Plaza içerisinde, toplantı odalı, kullanıma hazır ofis. Otoparklı ve 7/24 güvenlikli bina.",
                'created_at' => '2026-06-18 14:30:00',
            ],
            [
                'id' => 3,
                'title' => 'Bahçeli Müstakil Ev',
                'type' => 'Müstakil Ev',
                'status' => 'Satılık',
                'price' => 6800000,
                'location' => 'Çekmeköy, İstanbul',
                'area' => 220,
                'rooms' => '5+1',
                'description' => "Geniş bahçeli, özel havuzlu, doğa ile iç içe müstakil ev. Ana yola yakın, sakin bir mahallede.",
                'created_at' => '2026-06-15 09:15:00',
            ],
        ];

        $uploaded = [
            '1775949237_0_69dad5b50db72.jpeg',
            '1775949237_1_69dad5b50e87f.jpeg',
            '1775949237_3_69dad5b5104f0.jpeg',
            '1775949237_4_69dad5b510fe0.jpeg',
            '1775949237_5_69dad5b511b47.jpeg',
        ];

        $this->images = [
            1 => [$uploaded[0], $uploaded[1]],
            2 => [$uploaded[2]],
            3 => [$uploaded[3], $uploaded[4]],
        ];
    }

    private function imagesFor(int $propertyId, bool $onlyFirst): array
    {
        $names = $this->images[$propertyId] ?? [];
        $rows = [];
        foreach ($names as $i => $name) {
            $rows[] = ['id' => $propertyId * 10 + $i, 'property_id' => $propertyId, 'image_name' => $name, 'sort_order' => $i];
        }
        if ($onlyFirst) {
            return $rows ? [$rows[0]] : [];
        }
        return $rows;
    }

    private function resolve(string $sql, array $params = []): array
    {
        if (stripos($sql, 'FROM properties') !== false && stripos($sql, 'WHERE id') !== false) {
            $id = (int)($params['id'] ?? 0);
            foreach ($this->properties as $p) {
                if ($p['id'] === $id) {
                    return [$p];
                }
            }
            return [];
        }

        if (stripos($sql, 'FROM properties') !== false) {
            return $this->properties;
        }

        if (stripos($sql, 'FROM property_images') !== false) {
            $propertyId = (int)($params['property_id'] ?? 0);
            $onlyFirst = stripos($sql, 'LIMIT 1') !== false;
            return $this->imagesFor($propertyId, $onlyFirst);
        }

        return [];
    }

    public function query(string $sql): PreviewStatement
    {
        return new PreviewStatement($this->resolve($sql));
    }

    public function prepare(string $sql): PreviewPreparedStatement
    {
        return new PreviewPreparedStatement($this, $sql);
    }

    public function resolveFor(string $sql, array $params): array
    {
        return $this->resolve($sql, $params);
    }

    public function exec(string $sql): int
    {
        return 0;
    }

    public function lastInsertId(): string
    {
        return '1';
    }

    public function setAttribute($attr, $value): bool
    {
        return true;
    }
}

class PreviewPreparedStatement
{
    private PreviewPDO $pdo;
    private string $sql;
    private array $rows = [];
    private int $cursor = 0;

    public function __construct(PreviewPDO $pdo, string $sql)
    {
        $this->pdo = $pdo;
        $this->sql = $sql;
    }

    public function execute(array $params = []): bool
    {
        $this->rows = $this->pdo->resolveFor($this->sql, $params);
        $this->cursor = 0;
        return true;
    }

    public function fetch(int $mode = PDO::FETCH_ASSOC)
    {
        if (!isset($this->rows[$this->cursor])) {
            return false;
        }
        return $this->rows[$this->cursor++];
    }

    public function fetchAll(int $mode = PDO::FETCH_ASSOC): array
    {
        return $this->rows;
    }
}
