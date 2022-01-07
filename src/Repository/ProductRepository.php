<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getItemsTable($request, $paginator)
    {
        $draw = $request->query->get('draw','1');
        $length = $request->query->get('length','1');
        $start = $request->query->get('start','1');
        $page = $request->query->get('page', null);
        $search = $request->query->get('search','-');
        $columns = $request->query->get('columns',[]);        
        $order = $request->query->get('order',[]);

        $qb = $this->createQueryBuilder('p');

        /* Datatable Filters */
        foreach($columns as $col_index => $col_data){
            $fil_tipo = $col_data['search']['regex'];
            $fil_campo = $col_data['data'];
            $fil_valor = trim($col_data['search']['value']);
            
            if(!empty($fil_valor)){  

                if($fil_campo == 'code'){
                    $qb->andWhere("p.code = :code")
                    ->setParameter($fil_campo, $fil_valor);                       
                }elseif($fil_campo == 'name'){
                    $qb->andWhere('UPPER(p.name)  LIKE UPPER(:name)')
                        ->setParameter($fil_campo, '%'.$fil_valor.'%');                        
                }          
            }
        }        

        /* Datatable Order */
        if($order){
            foreach($order as $ca_dir){     
                $qb->orderBy('p.'.$columns[$ca_dir['column']]['data'], $ca_dir['dir']);
            } 
        }

        $page = $page ?: ceil($start / $length +1);

        $listado = $paginator->paginate(
            $qb,
            $page,
            $length
        );

        $listadoArray = [];
        foreach($listado->getItems() as $product){
            if($product){
                $listadoArray[] = [
                    'id' => $product->getId(),
                    'code' => $product->getCode(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'brand' => $product->getBrand(),
                    'price' => $product->getPrice(),
                    'createdAt' => date_format($product->getCreatedAt(), 'd-m-Y'),
                    'updatedAt' => date_format($product->getUpdatedAt(), 'd-m-Y'),
                    'category' => $product->getCategory()->getName(),
                ];
            }
        };

        $res = [
            'draw' => $draw,
            'pagina' => $page,
            'recordsTotal'=> $listado->getTotalItemCount(),
            'recordsFiltered' => $listado->getTotalItemCount(),
            'data' => $listadoArray,
        ];

        return $res;        
    }
}
