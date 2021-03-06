<?php

namespace ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
                                 //faz o 'import' da classe Constraints do Symfony, dando o apelido de Assert
                                                    //Este import permitirá utilizar a notação para não permitir campos nulos
                                                    //em alguns atributos
use ModelBundle\Entity\Timestampable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo; //Vamos configurar nossa entidade post, para que a mesma receba os slugs
// 'slug' == links permanentes que o blog terá para cada parte, sejam artigos, categorias, tags e páginas estáticas

//A anotação '@ORM\Entity(repositoryClass="ModelBundle\Repository\PostRepository")' abaixo indica onde está a classe PostRepository
/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="ModelBundle\Repository\PostRepository")
 */
class Post extends Timestampable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    // a notação '@Assert\NotBlank' faz o campo do atributo não permitir valores nulos

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=150)
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank
     */
    private $content;

    //Abaixo estamos fazendo o relacionamento da entidade Post com a entidade Author
    //Utilizamos a notação para fazer o relacionamento da variavel author

    /**
     * @var Author
     *
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="posts")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank
     */
    private $author;


    /**
     * @var string
     *
     * @ORM\Column(name="cover", type="string", length=255, nullable=true)
     */
    private $cover; //este atributo receberá o nome da imagem

    /**
     * @Assert\File(maxSize="1000000")
     */
    private $file; //este atributo receberá o arquivo da imagem como um limite de tamanho


    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"}, unique=false)
     * @ORM\Column(length=255)
     */
    private $slug; //criamos essa propriedade privada com as anotações acima para armazenar os slugs dos posts




    //Getter e setter dos atributos acima ($cover e $file)
    /**
     * Get cover
     *
     * @return string
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Set cover
     *
     * @param string $cover
     * @return Image
     */
    public function setCover($cover)
    {
        $this->cover = $cover;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null) //O setFile receberá um parâmetro $file, esse parâmetro
        // será do tipo UploadedFile, e caso não passamos uma imagem, poderá ser nulo
    {
        $this->file = $file;
    }


    /**
     * Relative path.
     * Get web path to upload directory.
     *
     * @return string
     */
    protected function getUploadPath()
    {
        //Precisamos obter o caminho relativo do upload, ou seja, a pasta para onde as imagens serão enviadas;
        // para isso vamos criar o método protegido getUploadPath(), que nos retornará essa pasta
        return 'uploads/covers';
    }

    /**
     * Absolute path.
     * Get absolute path to upload directory.
     *
     * @return string
     */
    protected function getUploadAbsolutePath()
    {
        //Temos que obter o caminho absoluto, para fazer o upload de nossas imagens, que ficará na pasta web,
        // para isso vamos criar o método protegido getUploadAbsolutePath(), que nos retornará o caminho absoluto,
        // e para chegarmos na pasta “uploads/covers”, vamos concatenar com o método getUploadPath() criado acima
        return __DIR__ . '/../../../web/' . $this->getUploadPath();
    }

    /**
     * Relative path.
     * Get web path to a cover.
     *
     * @return null|string
     */
    public function getCoverWeb()
    {
        //Agora precisamos apresentar o caminho de nossas imagens para as views, vamos criar o método
        //público getCoverWeb(), caso tenhamos uma imagem, ou seja, caso a imagem não seja nula, apresentamos a
        // imagem nas views, para isso usaremos o método getUploadPath(), concatenado com o nome de nossa imagem,
        // ou seja o método getCover()
        return null === $this->getCover()
            ? null
            : $this->getUploadPath() . '/' . $this->getCover();
    }

    /**
     * Get path on disk to a cover.
     *
     * @return null|string
     *   Absolute path.
     */
    public function getCoverAbsolute()
    {
        //Podemos precisar do caminho absoluto de nossa imagem, para isso vamos criar o método getCoverAbsolute(),
        // para obtermos esse caminho quando precisarmos
        return null === $this->getCover()
            ? null
            : $this->getUploadAbsolutePath() . '/' . $this->getCover();
    }

    /**
     * Upload a cover file.
     */
    public function upload()
    {
        //Agora criamos um método que fará o upload da imagem, para isso, criamos um método como nome upload(),
        // caso a imagem não seja nula, ele fará o upload usando alguns métodos prontos da classe UploadedFile,
        // para mover a imagens
        if (null === $this->getFile()) {
            return;
        }
        $filename = $this->getFile()->getClientOriginalName();
        $this->getFile()->move($this->getUploadAbsolutePath(), $filename);
        $this->setCover($filename);
        $this->setFile();
    }


//    Não precisamos mais do conteúdo abaixo, já que esta classe está herdando
//da Classe Abstrata Timestampable

//    /**
//     * @var \DateTime
//     *
//     * @ORM\Column(name="created_at", type="datetime")
//     */
//    private $createdAt;
//
//    /**
//     * @var \DateTime
//     *
//     * @ORM\Column(name="update_at", type="datetime")
//     */
//    private $updateAt;


//    /*
//
//        Precisamos que, ao criarmos nosso post, seja inserido
//        automaticamente a data de criação, e a data de atualização,
//        para isso vamos criar um método construtor em nossa entidade,
//        veja abaixo:
//
//    */

//    Uma vez importada a Classe Timestampable, não é mais necessário a existência deste construtor

//    public function __construct()  //construtor
//    {
//        $this->createdAt = new \DateTime();
//        $this->updatedAt = new \DateTime();
//    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

//    Como removemos os atributos createdAt e updateAt, os métodos abaixo não serão mais necessários

//    /**
//     * Set createdAt
//     *
//     * @param \DateTime $createdAt
//     *
//     * @return Post
//     */
//    public function setCreatedAt($createdAt)
//    {
//        $this->createdAt = $createdAt;
//
//        return $this;
//    }
//
//    /**
//     * Get createdAt
//     *
//     * @return \DateTime
//     */
//    public function getCreatedAt()
//    {
//        return $this->createdAt;
//    }
//
//    /**
//     * Set updateAt
//     *
//     * @param \DateTime $updateAt
//     *
//     * @return Post
//     */
//    public function setUpdateAt($updateAt)
//    {
//        $this->updateAt = $updateAt;
//
//        return $this;
//    }
//
//    /**
//     * Get updateAt
//     *
//     * @return \DateTime
//     */
//    public function getUpdateAt()
//    {
//        return $this->updateAt;
//    }

    /**
     * Set author
     *
     * @param \ModelBundle\Entity\Author $author
     *
     * @return Post
     */
    public function setAuthor(\ModelBundle\Entity\Author $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \ModelBundle\Entity\Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Post
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
