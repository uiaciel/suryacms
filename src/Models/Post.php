<?php

namespace Uiaciel\SuryaCMS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id',
        'translation_id',
        'user_id',
        'category_id',
        'title',
        'slug',
        'content',
        'datepublish',
        'tags',
        'source_url',
        'source_favicon',
        'source_title',
        'feature',
        'flash',
        'view',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function gambar()
    {
        preg_match_all('@src="([^"]+)"@', $this->content, $match);
        $src = array_pop($match);

        return $src;
    }

    public function getKeywordsAttribute()
    {
        $content = strip_tags($this->content);

        // Hilangkan tanda baca, ubah ke lowercase
        $clean = strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', '', $content));

        // Pisahkan kata
        $words = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);

        // Daftar stopwords (bisa ditambah)
        $stopwords = [
            // Bahasa Indonesia
            'dan',
            'atau',
            'yang',
            'di',
            'ke',
            'dari',
            'untuk',
            'dengan',
            'pada',
            'adalah',
            'ini',
            'itu',
            'sebagai',
            'oleh',
            'dalam',
            'juga',
            'karena',
            'tetapi',
            'agar',
            'sehingga',
            'bagi',
            'saat',
            'dapat',
            'akan',
            'tidak',
            'sudah',
            'belum',
            'masih',
            'lebih',
            'kurang',
            // Bahasa Inggris
            'the',
            'and',
            'or',
            'but',
            'if',
            'while',
            'with',
            'without',
            'to',
            'from',
            'in',
            'on',
            'at',
            'by',
            'for',
            'of',
            'is',
            'are',
            'was',
            'were',
            'be',
            'been',
            'being',
            'as',
            'an',
            'a',
            'that',
            'this',
            'these',
            'those',
            'it',
            'its',
            'their',
            'they',
            'them',
            'he',
            'she',
            'his',
            'her',
            'you',
            'your',
            'we',
            'our',
            'us',
            'i',
            'me',
            'my',
            'mine',
            'do',
            'does',
            'did',
            'so',
            'such',
            'not',
            'no',
            'yes',
            'can',
            'could',
            'should',
            'would',
            'will',
            'just',
            'about',
            'into',
            'out',
            'up',
            'down',
            'over',
            'under',
            'again',
            'once',
            'then',
            'than',
            'too',
            'very',
            'also',
            'only',
            'all',
            'any',
            'both',
            'each',
            'few',
            'more',
            'most',
            'other',
            'some',
            'such',
            'nor',
            'how',
            'when',
            'where',
            'why',
            'what',
            'which',
            'who',
            'whom',
            'whose',
            'because',
        ];

        // Filter stopwords
        $filtered = array_filter($words, function ($word) use ($stopwords) {
            return ! in_array($word, $stopwords);
        });

        // Hitung frekuensi
        $freq = array_count_values($filtered);

        // Urutkan berdasarkan frekuensi
        arsort($freq);

        // Ambil 10 kata teratas
        $topWords = array_slice(array_keys($freq), 0, 10);

        // Gabungkan jadi string
        return implode(', ', $topWords);
    }
}
