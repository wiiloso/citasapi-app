<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



/**
 * @OA\Info(
 *   swagger: "2.0"
 *   info: {
 *       "title": "API Eventos Clínica CEMEDSSO",
 *       "version": "1.0.0",
 *       "description": "Documentación OpenAPI (Swagger) Eventos Clínica (Proyecto Integrador) CEMEDSSO"
 *   }
 * )
 */
class EventController extends Controller
{
    // localhost:8000/api/eventos/getall
    /**
     * @OA\Get(
     *     path="/api/eventos/getall",
     *     summary="Obtener todos las citas médicas",
     *     description="Recupera una lista de todos las citas médicas.",
     *     operationId="operationIdHere",
     *     tags={"Eventos"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="propertyName",
     *                 type="string",
     *                 description="Description of the property"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function index()
    {
        $events = DB::select('select * from events');
        return response()->json([
            'status' => true,
            'message' => 'Lista Eventos recuperada con éxito',
            'data' => $events
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/eventos/getdata/{id}",
     *     summary="Obtener una cita médica por ID",
     *     description="Recupera los detalles de una cita médica específica utilizando su ID.",
     *     operationId="getEventById",
     *     tags={"Eventos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la cita médica",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Indica si la solicitud fue exitosa"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Mensaje descriptivo"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Event")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Evento no encontrado"
     *     )
     * )
     */

    public function show($id)
    {
        $event = DB::select('select * from events where id = ?', [$id]);
        if ($event) {
            return response()->json([
                'status' => true,
                'message' => 'Evento recuperado con éxito',
                'data' => $event
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Evento no encontrado',
                'data' => null
            ], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/eventos/savedata",
     *     summary="Guardar un nuevo evento",
     *     description="Crea un nuevo evento en el sistema.",
     *     operationId="storeEvent",
     *     tags={"Eventos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 description="Título del evento"
     *             ),
     *             @OA\Property(
     *                 property="start",
     *                 type="string",
     *                 format="date-time",
     *                 description="Fecha y hora de inicio del evento"
     *             ),
     *             @OA\Property(
     *                 property="end",
     *                 type="string",
     *                 format="date-time",
     *                 description="Fecha y hora de fin del evento"
     *             ),
     *             @OA\Property(
     *                 property="color",
     *                 type="string",
     *                 description="Color del evento"
     *             ),
     *             @OA\Property(
     *                 property="user_id",
     *                 type="integer",
     *                 description="ID del usuario"
     *             ),
     *             @OA\Property(
     *                 property="doctor_id",
     *                 type="integer",
     *                 description="ID del doctor"
     *             ),
     *             @OA\Property(
     *                 property="consultorio_id",
     *                 type="integer",
     *                 description="ID del consultorio"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Evento creado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Indica si la solicitud fue exitosa"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Mensaje descriptivo"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     description="ID del evento creado"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request)
    {
        // -title, start, end, color, user_id, doctor_id, consultorio_id
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'start' => 'required|date',
            'end' => 'required|date',
            'color' => 'required|string|max:7',
            'user_id' => 'required|integer|exists:users,id',
            'doctor_id' => 'required|integer|exists:doctors,id',
            'consultorio_id' => 'required|integer|exists:consultorios,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $event = DB::insert('insert into events (title, start, end, color, user_id, doctor_id, consultorio_id) values (?, ?, ?, ?, ?, ?, ?)', [
            $request->title,
            $request->start,
            $request->end,
            $request->color,
            $request->user_id,
            $request->doctor_id,
            $request->consultorio_id
        ]);

        if (!$event) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear el evento  ',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Evento creado con éxito',
            'data' => $event
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/eventos/updatedata/{id}",
     *     summary="Actualizar un evento existente",
     *     description="Actualiza los detalles de un evento existente utilizando su ID.",
     *     operationId="updateEvent",
     *     tags={"Eventos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del evento a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 description="Título del evento"
     *             ),
     *             @OA\Property(
     *                 property="start",
     *                 type="string",
     *                 format="date-time",
     *                 description="Fecha y hora de inicio del evento"
     *             ),
     *             @OA\Property(
     *                 property="end",
     *                 type="string",
     *                 format="date-time",
     *                 description="Fecha y hora de fin del evento"
     *             ),
     *             @OA\Property(
     *                 property="color",
     *                 type="string",
     *                 description="Color del evento"
     *             ),
     *             @OA\Property(
     *                 property="user_id",
     *                 type="integer",
     *                 description="ID del usuario"
     *             ),
     *             @OA\Property(
     *                 property="doctor_id",
     *                 type="integer",
     *                 description="ID del doctor"
     *             ),
     *             @OA\Property(
     *                 property="consultorio_id",
     *                 type="integer",
     *                 description="ID del consultorio"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Evento actualizado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Indica si la solicitud fue exitosa" 
     *            ),
     *            @OA\Property(
     *                property="message",
     *                type="string",
     *                description="Mensaje descriptivo"
     *            )
     *        )
     *    )
     * )
     */
    public function update(Request $request, $pro_id)
    {
        // -title, start, end, color, user_id, doctor_id, consultorio_id
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'start' => 'required|date',
            'end' => 'required|date',
            'color' => 'required|string|max:7',
            'user_id' => 'required|integer|exists:users,id',
            'doctor_id' => 'required|integer|exists:doctors,id',
            'consultorio_id' => 'required|integer|exists:consultorios,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $event = DB::update('update events set title = ?, start = ?, end = ?, color = ?, user_id = ?, doctor_id = ?, consultorio_id = ? where pro_id = ?', [
            $request->title,
            $request->start,
            $request->end,
            $request->color,
            $request->user_id,
            $request->doctor_id,
            $request->consultorio_id,
            $pro_id
        ]);

        if ($event == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Evento no encontrado',
            ], 404);
        }

        if (!$event) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el evento',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Evento actualizado con éxito',
            'data' => $event
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/eventos/deletedata/{id}",
     *     summary="Eliminar un evento existente",
     *     description="Elimina un evento existente utilizando su ID.",
     *     operationId="deleteEvent",
     *     tags={"Eventos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del evento a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Evento eliminado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Indica si la solicitud fue exitosa"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Mensaje descriptivo"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     description="ID del evento eliminado"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Evento no encontrado"
     *     )
     * )
     */

    public function destroy($id)
    {
        // -title, start, end, color, user_id, doctor_id, consultorio_id
        $event = DB::delete('delete from events where pro_id = ?', [$id]);

        if ($event === 0) {
            return response()->json([
                'status' => false,
                'message' => 'Evento no encontrado',
            ], 404);
        }

        if (!$event) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el evento',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Evento eliminado con éxito',
            'data' => $event
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/eventos/updatestate/{id}",
     *     summary="Actualizar el estado de un evento existente",
     *     description="Actualiza el estado de un evento existente utilizando su ID.",
     *     operationId="updateEventState",
     *     tags={"Eventos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del evento a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado del evento actualizado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Indica si la solicitud fue exitosa"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Mensaje descriptivo"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     description="ID del evento actualizado"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Evento no encontrado"
     *     )
     * )
    */

    public function updatestate($pro_id)
    {
        // -title, start, end, color, user_id, doctor_id, consultorio_id
        $event = DB::update('update events set estado = 0 where pro_id = ?', [$pro_id]);

        if ($event === 0) {
            return response()->json([
                'status' => false,
                'message' => 'Evento no encontrado',
            ], 404);
        }

        if (!$event) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el evento',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Evento eliminado con éxito',
            'data' => $event
        ], 200);
    }
}
    // -title, start, end, color, user_id, doctor_id, consultorio_id