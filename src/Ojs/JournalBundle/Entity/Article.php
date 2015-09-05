<?php

namespace Ojs\JournalBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as JMS;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use Ojs\AnalyticsBundle\Entity\ArticleStatistic;
use Ojs\CoreBundle\Entity\GenericEntityTrait;
use Ojs\CoreBundle\Params\CommonParams;
use Ojs\UserBundle\Entity\User;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

/**
 * Article.
 *
 * @GRID\Source(columns="id ,title, issue, doi, journal.title, pubdate, section.title")
 * @GRID\Source(columns="id, status, title, journal.title", groups={"submission"})
 * @ExclusionPolicy("all")
 */
class Article extends AbstractTranslatable
{
    use GenericEntityTrait;
    /**
     * auto-incremented article unique id.
     *
     * @GRID\Column(title="id")
     * @Expose
     * @Groups({"JournalDetail","IssueDetail","ArticleDetail"})
     */
    protected $id;
    /**
     * (optional) English transliterated abstract.
     *
     * @var string
     * @Expose
     */
    protected $abstractTransliterated;
    /**
     * @Prezent\Translations(targetEntity="Ojs\JournalBundle\Entity\ArticleTranslation")
     */
    protected $translations;
    /**
     * @var int
     * @Expose
     */
    private $articleTypeId;
    /**
     * @var int
     * @Expose
     * @GRID\Column(type="text", groups={"submission"})
     */
    private $status;
    /**
     * (optional).
     *
     * @var string
     * @GRID\Column(title="OAI")
     * @Expose
     * @Groups({"JournalDetail","IssueDetail","ArticleDetail"})
     */
    private $doi;
    /**
     * Could contain any article ID used by the provider.
     *
     * @var string
     */
    private $otherId;
    /**
     * @var int
     * @Expose
     */
    private $journalId;
    /**
     * Original article title.
     *
     * @var string
     * @GRID\Column(title="title")
     * @Expose
     * @Groups({"JournalDetail","IssueDetail","ArticleDetail"})
     */
    private $title;
    /**
     * Roman transliterated title.
     *
     * @var string
     * @Expose
     * @Groups({"JournalDetail","IssueDetail","ArticleDetail"})
     */
    private $titleTransliterated;
    /**
     * @var string
     * @Expose
     * @Groups({"JournalDetail","IssueDetail","ArticleDetail"})
     */
    private $subtitle;
    /**
     * @var string
     * @Expose
     * @Groups({"JournalDetail","IssueDetail","ArticleDetail"})
     */
    private $keywords;
    /**
     * Some articles carries no authorship.
     *
     * @var bool
     * @Expose
     * @Groups({"JournalDetail","IssueDetail","ArticleDetail"})
     */
    private $isAnonymous;
    /**
     * @var \DateTime
     * @Expose
     * @Groups({"JournalDetail","IssueDetail","ArticleDetail"})
     */
    private $submissionDate;
    /**
     * @var \DateTime
     * @GRID\Column(title="pubdate")
     * @Expose
     * @Groups({"JournalDetail","IssueDetail","ArticleDetail"})
     */
    private $pubdate;
    /**
     * @var string
     * @Expose
     */
    private $pubdateSeason;
    /**
     * @var string
     * @Expose
     * @Groups({"JournalDetail","IssueDetail","ArticleDetail"})
     */
    private $part;
    /**
     * @var int
     * @Expose
     * @Groups({"IssueDetail","ArticleDetail"})
     */
    private $firstPage;
    /**
     * @var int
     * @Expose
     * @Groups({"IssueDetail","ArticleDetail"})
     */
    private $lastPage;
    /**
     * @var string
     * @JMS\Expose
     */
    private $uri;
    /**
     * @var string
     * @Expose
     * @Groups({"IssueDetail","ArticleDetail"})
     */
    private $primaryLanguage;
    /**
     * @var int
     * @Expose
     * @Groups({"IssueDetail","ArticleDetail"})
     */
    private $orderNum;
    /**
     * Original abstract.
     *
     * @var string
     * @Expose
     * @Groups({"IssueDetail","ArticleDetail"})
     */
    private $abstract;
    /**
     * @var string
     * @Expose
     */
    private $subjects;
    /**
     * @var Collection|Lang[]
     * @Expose
     * @Groups({"IssueDetail","ArticleDetail"})
     */
    private $languages;
    /**
     * @var Issue
     * @GRID\Column(title="issue Title")
     */
    private $issue;
    /**
     * @var ArticleTypes
     */
    private $articleType;
    /**
     * @var Collection|Citation[]
     * @Expose
     * @Groups({"IssueDetail","ArticleDetail"})
     */
    private $citations;
    /**
     * @var Journal
     */
    private $journal;
    /**
     * @var JournalSection
     */
    private $section;
    /**
     * @var User
     */
    private $submitterUser;
    /**
     * @var int
     * @Expose
     */
    private $sectionId;
    /**
     * arbitrary attributes.
     *
     * @var Collection|ArticleAttribute[]
     */
    private $attributes;
    /**
     * @var ArrayCollection|ArticleAuthor[]
     * @Groups({"IssueDetail","ArticleDetail"})
     */
    private $articleAuthors;
    /**
     * @var Collection|ArticleFile[]
     * @Expose
     */
    private $articleFiles;
    /**
     * @var string
     */
    private $header;
    /**
     * @var string
     */
    private $slug;
    /**
     * @var bool
     */
    private $setupStatus;

    /** @var  string */
    private $note;

    /**
     * @var ArrayCollection|ArticleStatistic[]
     */
    private $statistics;

    /**
     * @var string
     */
    private $publicURI;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->citations = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->articleAuthors = new ArrayCollection();
        $this->articleFiles = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     *
     * @return $this
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function addAttribute($name, $value)
    {
        $this->attributes[$name] = new ArticleAttribute($name, $value, $this);

        return $this;
    }

    /**
     * @param $name
     *
     * @return bool|ArticleAttribute
     */
    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : false;
    }

    /**
     * Get subjects.
     *
     * @return string
     */
    public function getSubjects()
    {
        return $this->translate()->getSubjects();
    }

    /**
     * Set subjects.
     *
     * @param string $subjects
     *
     * @return $this
     */
    public function setSubjects($subjects = null)
    {
        $this->translate()->setSubjects($subjects);

        return $this;
    }

    /**
     * Translation helper method.
     *
     * @param null $locale
     *
     * @return mixed|null|\Ojs\JournalBundle\Entity\ArticleTranslation
     */
    public function translate($locale = null)
    {
        if (null === $locale) {
            $locale = $this->currentLocale;
        }
        if (!$locale) {
            throw new \RuntimeException('No locale has been set and currentLocale is empty');
        }
        if ($this->currentTranslation && $this->currentTranslation->getLocale() === $locale) {
            return $this->currentTranslation;
        }
        /** @var ArticleTranslation $defaultTranslation */
        $defaultTranslation = $this->translations->get($this->getDefaultLocale());
        if (!$translation = $this->translations->get($locale)) {
            $translation = new ArticleTranslation();
            if (!is_null($defaultTranslation)) {
                $translation->setTitle($defaultTranslation->getTitle());
                $translation->setAbstract($defaultTranslation->getAbstract());
                $translation->setKeywords($defaultTranslation->getKeywords());
                $translation->setSubjects($defaultTranslation->getSubjects());
                $translation->setSubtitle($defaultTranslation->getSubtitle());
            }
            $translation->setLocale($locale);
            $this->addTranslation($translation);
        }
        $this->currentTranslation = $translation;

        return $translation;
    }

    /**
     * @param Lang $language
     *
     * @return $this
     */
    public function addLanguage(Lang $language)
    {
        $this->languages[] = $language;

        return $this;
    }

    /**
     * @param Lang $language
     *
     * @return $this
     */
    public function removeLanguage(Lang $language)
    {
        $this->languages->removeElement($language);

        return $this;
    }

    /**
     * @return Collection|Lang[]
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @return Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * @param Issue $issue
     *
     * @return $this
     */
    public function setIssue(Issue $issue = null)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * @return ArticleTypes
     */
    public function getArticleType()
    {
        return $this->articleType;
    }

    /**
     * @param ArticleTypes $articleType
     *
     * @return $this
     */
    public function setArticleType(ArticleTypes $articleType)
    {
        $this->articleType = $articleType;

        return $this;
    }

    /**
     * @return ArrayCollection|ArticleAuthor[]
     */
    public function getArticleAuthors()
    {
        return $this->articleAuthors;
    }

    /**
     * @return Collection|ArticleFile[]
     */
    public function getArticleFiles()
    {
        return $this->articleFiles;
    }

    /**
     * Add citation.
     *
     * @param Citation $citation
     *
     * @return $this
     */
    public function addCitation(Citation $citation)
    {
        if (!$this->citations->contains($citation)) {
            $this->citations->add($citation);
            $citation->addArticle($this);
        }

        return $this;
    }

    /**
     * Remove citation.
     *
     * @param Citation $citation
     *
     * @return $this
     */
    public function removeCitation(Citation $citation)
    {
        if ($this->citations->contains($citation)) {
            $this->citations->removeElement($citation);
            $citation->removeArticle($this);
        }

        return $this;
    }

    /**
     * Get citations.
     *
     * @return Collection|Citation[]
     */
    public function getCitations()
    {
        return $this->citations;
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
        return CommonParams::statusText($this->status);
    }

    /**
     * @return string
     */
    public function getStatusColor()
    {
        return CommonParams::statusColor($this->status);
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrderNum()
    {
        return $this->orderNum;
    }

    /**
     * @param int $orderNum
     *
     * @return $this
     */
    public function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryLanguage()
    {
        return $this->primaryLanguage;
    }

    /**
     * @param string $primaryLanguage
     *
     * @return $this
     */
    public function setPrimaryLanguage($primaryLanguage)
    {
        $this->primaryLanguage = $primaryLanguage;

        return $this;
    }

    /**
     * @return User
     */
    public function getSubmitterUser()
    {
        return $this->submitterUser;
    }

    /**
     * @param User $submitterUser
     *
     * @return $this
     */
    public function setSubmitterUser(User $submitterUser)
    {
        $this->submitterUser = $submitterUser;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->translate()->getKeywords();
    }

    /**
     * @param $keywords
     *
     * @return $this
     */
    public function setKeywords($keywords)
    {
        $this->translate()->setKeywords($keywords);

        return $this;
    }

    /**
     * Get doi.
     *
     * @return string
     */
    public function getDoi()
    {
        return $this->doi;
    }

    /**
     * Set doi.
     *
     * @param string $doi
     *
     * @return $this
     */
    public function setDoi($doi)
    {
        $this->doi = $doi;

        return $this;
    }

    /**
     * Get otherId.
     *
     * @return string
     */
    public function getOtherId()
    {
        return $this->otherId;
    }

    /**
     * Set otherId.
     *
     * @param string $otherId
     *
     * @return $this
     */
    public function setOtherId($otherId)
    {
        $this->otherId = $otherId;

        return $this;
    }

    /**
     * Get articleTypeId.
     *
     * @return int
     */
    public function getArticleTypeId()
    {
        return $this->articleTypeId;
    }

    /**
     * Set articleTypeId.
     *
     * @param int $articleTypeId
     *
     * @return $this
     */
    public function setArticleTypeId($articleTypeId)
    {
        $this->articleTypeId = $articleTypeId;

        return $this;
    }

    /**
     * Get journalId.
     *
     * @return int
     */
    public function getJournalId()
    {
        return $this->journalId;
    }

    /**
     * Set journalId.
     *
     * @param int $journalId
     *
     * @return $this
     */
    public function setJournalId($journalId)
    {
        $this->journalId = $journalId;

        return $this;
    }

    /**
     * Get journal.
     *
     * @return Journal
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * Set journal.
     *
     * @param Journal $journal
     *
     * @return $this
     */
    public function setJournal(Journal $journal)
    {
        $this->journal = $journal;

        return $this;
    }

    /**
     * Get sectionId.
     *
     * @return int
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    /**
     * Set sectionId.
     *
     * @param int $sectionId
     *
     * @return $this
     */
    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    /**
     * Get section.
     *
     * @return JournalSection
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set section.
     *
     * @param JournalSection $section
     *
     * @return $this
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get setupStatus.
     *
     * @return bool
     */
    public function getSetupStatus()
    {
        return $this->setupStatus;
    }

    /**
     * Set setupStatus.
     *
     * @param string $setupStatus
     *
     * @return $this
     */
    public function setSetupStatus($setupStatus)
    {
        $this->setupStatus = $setupStatus;

        return $this;
    }

    /**
     * Get titleTransliterated.
     *
     * @return string
     */
    public function getTitleTransliterated()
    {
        return $this->titleTransliterated;
    }

    /**
     * Set titleTransliterated.
     *
     * @param string $titleTransliterated
     *
     * @return $this
     */
    public function setTitleTransliterated($titleTransliterated)
    {
        $this->titleTransliterated = $titleTransliterated;

        return $this;
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->translate()->getSubtitle();
    }

    /**
     * Set subtitle.
     *
     * @param string $subtitle
     *
     * @return $this
     */
    public function setSubtitle($subtitle)
    {
        $this->translate()->setSubtitle($subtitle);

        return $this;
    }

    /**
     * Get isAnonymous.
     *
     * @return bool
     */
    public function getIsAnonymous()
    {
        return $this->isAnonymous;
    }

    /**
     * Set isAnonymous.
     *
     * @param bool $isAnonymous
     *
     * @return $this
     */
    public function setIsAnonymous($isAnonymous)
    {
        $this->isAnonymous = $isAnonymous;

        return $this;
    }

    /**
     * Get pubdate.
     *
     * @return \DateTime
     */
    public function getPubdate()
    {
        return $this->pubdate;
    }

    /**
     * Set pubdate.
     *
     * @param \DateTime $pubdate
     *
     * @return $this
     */
    public function setPubdate($pubdate)
    {
        $this->pubdate = $pubdate;

        return $this;
    }

    /**
     * Get submissionDate.
     *
     * @return \DateTime
     */
    public function getSubmissionDate()
    {
        return $this->submissionDate;
    }

    /**
     * Set submissionDate.
     *
     * @param \DateTime $submissionDate
     *
     * @return $this
     */
    public function setSubmissionDate($submissionDate)
    {
        $this->submissionDate = $submissionDate;

        return $this;
    }

    /**
     * Get pubdateSeason.
     *
     * @return string
     */
    public function getPubdateSeason()
    {
        return $this->pubdateSeason;
    }

    /**
     * Set pubDateSeason.
     *
     * @param string $pubDateSeason
     *
     * @return $this
     */
    public function setPubdateSeason($pubDateSeason)
    {
        $this->pubdateSeason = $pubDateSeason;

        return $this;
    }

    /**
     * Get part.
     *
     * @return string
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * Set part.
     *
     * @param string $part
     *
     * @return $this
     */
    public function setPart($part)
    {
        $this->part = $part;

        return $this;
    }

    /**
     * Get firstPage.
     *
     * @return int
     */
    public function getFirstPage()
    {
        return $this->firstPage;
    }

    /**
     * Set firstPage.
     *
     * @param int $firstPage
     *
     * @return $this
     */
    public function setFirstPage($firstPage)
    {
        $this->firstPage = $firstPage;

        return $this;
    }

    /**
     * Get lastPage.
     *
     * @return int
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }

    /**
     * Set lastPage.
     *
     * @param int $lastPage
     *
     * @return $this
     */
    public function setLastPage($lastPage)
    {
        $this->lastPage = $lastPage;

        return $this;
    }

    /**
     * Get uri.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set uri.
     *
     * @param string $uri
     *
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get abstract.
     *
     * @return string
     */
    public function getAbstract()
    {
        return $this->translate()->getAbstract();
    }

    /**
     * Set abstract.
     *
     * @param string $abstract
     *
     * @return $this
     */
    public function setAbstract($abstract)
    {
        $this->translate()->setAbstract($abstract);

        return $this;
    }

    /**
     * Get abstractTransliterated.
     *
     * @return string
     */
    public function getAbstractTransliterated()
    {
        return $this->abstractTransliterated;
    }

    /**
     * Set abstractTransliterated.
     *
     * @param string $abstractTransliterated
     *
     * @return $this
     */
    public function setAbstractTransliterated($abstractTransliterated)
    {
        $this->abstractTransliterated = $abstractTransliterated;

        return $this;
    }

    /**
     * Remove attributes.
     *
     * @param ArticleAttribute $attributes
     */
    public function removeAttribute(ArticleAttribute $attributes)
    {
        $this->attributes->removeElement($attributes);
    }

    /**
     * Get attributes.
     *
     * @return Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Add articleAuthor.
     *
     * @param ArticleAuthor $articleAuthor
     *
     * @return $this
     */
    public function addArticleAuthor(ArticleAuthor $articleAuthor)
    {
        if (!$this->articleAuthors->contains($articleAuthor)) {
            $this->articleAuthors->add($articleAuthor);
            $articleAuthor->setArticle($this);
        }

        return $this;
    }

    /**
     * Remove articleAuthor.
     *
     * @param ArticleAuthor $articleAuthor
     *
     * @return $this
     */
    public function removeArticleAuthor(ArticleAuthor $articleAuthor)
    {
        if ($this->articleAuthors->contains($articleAuthor)) {
            $this->articleAuthors->removeElement($articleAuthor);
        }

        return $this;
    }

    /**
     * Add articleFiles.
     *
     * @param ArticleFile $articleFile
     *
     * @return $this
     */
    public function addArticleFile(ArticleFile $articleFile)
    {
        if (!$this->articleFiles->contains($articleFile)) {
            $this->articleFiles->add($articleFile);
            $articleFile->setArticle($this);
        }

        return $this;
    }

    /**
     * Remove articleFiles.
     *
     * @param ArticleFile $articleFile
     */
    public function removeArticleFile(ArticleFile $articleFile)
    {
        if ($this->articleFiles->contains($articleFile)) {
            $this->articleFiles->removeElement($articleFile);
        }
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle()."[#{$this->getId()}]";
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Article
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Set updated.
     *
     * @param \DateTime $updated
     *
     * @return Article
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return ArrayCollection|\Ojs\AnalyticsBundle\Entity\ArticleStatistic[]
     */
    public function getStatistics()
    {
        return $this->statistics;
    }

    /**
     * @param ArrayCollection|\Ojs\AnalyticsBundle\Entity\ArticleStatistic[] $statistics
     */
    public function setStatistics($statistics)
    {
        $this->statistics = $statistics;
    }

    /**
     * Returns the article's view count.
     *
     * @return int
     */
    public function getViewCount()
    {
        $count = 0;

        if ($this->statistics != null) {
            foreach ($this->statistics as $stat) {
                $count += $stat->getView();
            }
        }

        return $count;
    }

    /**
     * @return string
     */
    public function getPublicURI()
    {
        return $this->publicURI;
    }

    /**
     * @param string $publicURI
     */
    public function setPublicURI($publicURI)
    {
        $this->publicURI = $publicURI;
    }
}
